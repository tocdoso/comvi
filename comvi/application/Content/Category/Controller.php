<?php
namespace Content\Category;
use Comvi\Core\Exception\HttpNotFoundException;
use Comvi\Core\AbstractController;
use Comvi\Core\Response;
use Comvi\Core\SessionManagerAwareInterface;
use Comvi\Core\SessionManagerAwareTrait;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\CurrentURLAwareInterface;
use Comvi\Core\CurrentURLAwareTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;
use Comvi\Core\DocumentAwareInterface;
use Comvi\Core\DocumentAwareTrait;
use Comvi\ORM\EntityManagerAwareInterface;
use Comvi\ORM\EntityManagerAwareTrait;

use PFBC\Form;
use PFBC\Element;
use PFBC\Validation;
use PFBC\View;

use Entity\Content\Category;
use Entity\Content\CategoryTranslation;
use Doctrine\ORM\Query;
use Gedmo\Translatable\TranslatableListener;

/**
 * Category Controller
 */
class Controller extends AbstractController implements SessionManagerAwareInterface, URLHelperInterface, CurrentURLAwareInterface, RouterAwareInterface, TranslatorAwareInterface, DocumentAwareInterface, EntityManagerAwareInterface
{
	use SessionManagerAwareTrait;
	use URLHelperTrait;
	use CurrentURLAwareTrait;
	use RouterAwareTrait;
	use TranslatorAwareTrait;
	use DocumentAwareTrait;
	use EntityManagerAwareTrait;

	public function init()
	{
		$this->repo	= $this->em->getRepository('Entity\\Content\\Category');
		$this->addTranslationModule('Content');
	}

	public function getAdd()
	{
		$form = $this->getForm();
		$view = $this->getView('edit');
		$view->assign('title',  $this->_('Add category', 'Content'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function afterGetAdd($response)
	{
		$this->breadcrumbs = array(
			array(
				'name'	=>  $this->_('Content', 'Content'),
				'url'	=> '?controller=Content/Controller&task=Edit'
			),
			array(
				'name'	=>  $this->_('Category', 'Content'),
				'url'	=> '?controller=Content/Category/Controller&task=Edit'
			),
			array(
				'name'	=>  $this->_('Add', 'default')
			)
		);

		return $response;
	}

	public function postAdd()
	{
		$redirect = '?controller=Content/Category/Controller&task=Add';

		if (Form::isValid('editCategory')) {
			$parent = $this->getCategory($_POST['parent']);
			$category = new Category;
			$category->fromArray($_POST);
			$category->setParent($parent);
			$this->em->persist($category);
			$this->em->flush();
			$redirect = '?controller=Content/Category/Controller&task=Edit';
		}

		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->build($this->url($redirect)));

		return $response;
	}

	public function getEdit($id = null)
	{
		if ($id == null) {
			return $this->getEditList();
		}

		$category = $this->getCategory($id);
		if ($category === null) {
			throw new HttpNotFoundException;
		}
		$this->id = $id;
		$this->title = $category->getTitle();

		$form = $this->getForm($category);
		$form->setValues($category->toArray());
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Edit category', 'Content'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function getEditList()
	{
		$root_node = $this->getParam('root') ? $this->getCategory($this->getParam('root')) : null;
		$view = $this->getView('list_edit');
		$view->assign('title', $root_node ? $root_node->getTitle() : $this->_('Edit categories', 'Content'));
		$view->assign('categories', $this->repo->childrenHierarchy($root_node));
		$view->assign('root', $root_node);

		return $view;
	}

	public function afterGetEdit($response)
	{
		if (!isset($this->id)) {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('Content', 'Content'),
					'url'	=> '?controller=Content/Controller&task=Edit'
				),
				array(
					'name'	=> $this->_('Category', 'Content')
				)
			);
		}
		else {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('Content', 'Content'),
					'url'	=> '?controller=Content/Controller&task=Edit'
				),
				array(
					'name'	=> $this->_('Category', 'Content'),
					'url'	=> '?controller=Content/Category/Controller&task=Edit'
				),
				array(
					'name'	=> $this->title
				)
			);
		}

		return $response;
	}

	public function postEdit($id)
	{
		if (Form::isValid('editCategory')) {
			$parent = $this->getCategory($_POST['parent']);
			$category = $this->getCategory($id);
			$category->fromArray($_POST);
			$category->setParent($parent);
			$this->em->flush();
		}
		//else {
			$response = new Response;
			$response
				->setStatus(302)
				->setHeader('Location', $this->build($this->url('?com=Content/Category&task=Edit&id='.$id)));

			return $response;
		//}
	}

	protected function getForm($ignore = null)
	{
		$categories = $this->repo->childrenFlat();
		$categories = array('' => '&lt;root&gt;') + $categories;

		if ($ignore !== null) {
			unset($categories[$ignore->getId()]);
			foreach ($this->repo->children($ignore) as $child) {
				unset($categories[$child->getId()]);
			}
		}

		$this->document->addStyle('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css');
		$static_url = $this->document->static_url;
		$form = new Form("editCategory");
		$form->configure(array(
			"action" => $this->build($this->current_url),
			"prevent" => array('bootstrap', 'jQuery'),
			"resourcesPath" => substr($static_url, 0, -1)
		));
		$form->addElement(new Element\Textbox("Title:", "title", array(
			"required" => 1,
			"longDesc" => "The required property provides a shortcut for applying the Required class to the element's
			validation property.  If supported, the HTML5 required attribute will also provide client-side validation."
		)));
		$form->addElement(new Element\Textbox("Slug", "slug", array(
			"validation" => new Validation\AlphaNumeric,
			"longDesc" => "The AlphaNumeric validation class will verify that the element's submitted value contains only letters, 
			numbers, underscores, and/or hyphens."
		)));
		$form->addElement(new Element\Select("Parent", "parent", $categories));
		$form->addElement(new Element\Textarea("Description", "description"));
		$form->addElement(new Element\Button);
		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "history.go(-1);"
		)));

		return $form;
	}

	protected function getCategory($id)
	{
		return $this->repo->find((int) $id /*category id*/);
	}
}
?>