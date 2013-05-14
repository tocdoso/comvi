<?php
namespace Navigation\Item;
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

use Entity\Item;

/**
 * Menu Item Controller
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

	/**
	* Class constructor
	*/
	public function init()
	{
		$this->repo	= $this->em->getRepository('Entity\\Item');
		$this->addTranslationModule('Navigation');
	}

	public function getAdd()
	{
		$item = new Item;
		$values =  array('parent' => $this->getParam('parent')) + $item->toArray();
		$form = $this->getForm();
		$form->setValues($values);
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Add item', 'Navigation'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function afterGetAdd($response)
	{
		$this->breadcrumbs = array(
			array(
				'name'	=> $this->_('Navigation', 'Navigation'),
				'url'	=> '?controller=Navigation/Controller'
			),
			array(
				'name'	=> $this->_('Items', 'Navigation'),
				'url'	=> '?controller=Navigation/Item/Controller&task=Edit'
			),
			array(
				'name'	=> $this->_('Add', 'default')
			)
		);

		return $response;
	}

	public function postAdd()
	{
		$redirect = '?controller=Navigation/Item/Controller&task=Add';

		if (Form::isValid('editItem')) {
			$parent = $this->getItem($_POST['parent']);
			$item = new Item;
			$item->fromArray($_POST);
			$item->setParent($parent);
			$this->em->persist($item);
			$this->em->flush();
			$redirect = '?controller=Navigation/Item/Controller&task=Edit';
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

		$item = $this->getItem($id);
		if ($item === null) {
			throw new HttpNotFoundException();
		}
		$this->id = $id;
		$this->title = $item->getName();

		$form = $this->getForm($item);
		$form->setValues($item->toArray());
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Edit item', 'Navigation'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function getEditList()
	{
		$root_node = $this->getParam('root') ? $this->getItem($this->getParam('root')) : null;
		$view = $this->getView('list_edit');
		$view->assign('title', $root_node ? $root_node->getName() : $this->_('Edit items', 'Navigation'));
		$view->assign('items', $this->repo->childrenHierarchy($root_node));
		$view->assign('root', $root_node);

		return $view;
	}

	public function afterGetEdit($response)
	{
		if (!isset($this->id)) {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('Navigation', 'Navigation'),
					'url'	=> '?controller=Navigation/Controller'
				),
				array(
					'name'	=> $this->_('Items', 'Navigation'),
					'url'	=> '?controller=Navigation/Item/Controller&task=Edit'
				),
				array(
					'name'	=> $this->_('Edit', 'default')
				)
			);
		}
		else {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('Navigation', 'Navigation'),
					'url'	=> '?controller=Navigation/Controller'
				),
				array(
					'name'	=> $this->_('Items', 'Navigation'),
					'url'	=> '?controller=Navigation/Item/Controller&task=Edit'
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
		$redirect = '?controller=Navigation/Item/Controller&task=Edit&id='.$id;

		if (Form::isValid('editItem')) {
			$parent = $this->getItem($_POST['parent']);
			$item = $this->getItem($id);
			$item->fromArray($_POST);
			$item->setParent($parent);
			$this->em->flush();
			$redirect = '?controller=Navigation/Item/Controller&task=Edit';
		}

		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->build($this->url($redirect)));

		return $response;
	}

	public function getDelete($id)
	{
		$item = $this->getItem($id);
		if ($item === null) {
			throw new HttpNotFoundException();
		}

		$this->em->remove($item);
		$this->em->flush();

		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->build($this->url('?controller=Navigation/Item/Controller&task=Edit')));

		return $response;
	}

	protected function getForm($ignore = null)
	{
		$items = $this->repo->childrenFlat();
		$items = array('' => '&lt;root&gt;') + $items;

		if ($ignore !== null) {
			unset($items[$ignore->getId()]);
			foreach ($this->repo->children($ignore) as $child) {
				unset($items[$child->getId()]);
			}
		}

		$this->document->addStyle('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css');
		$static_url = $this->document->static_url;
		$form = new Form("editItem");
		$form->configure(array(
			"action" => $this->build($this->current_url),
			"prevent" => array('bootstrap', 'jQuery'),
			"resourcesPath" => substr($static_url, 0, -1)
		));
		$form->addElement(new Element\Textbox("Name", "name", array(
			"required" => 1,
			"longDesc" => "The required property provides a shortcut for applying the Required class to the element's
			validation property.  If supported, the HTML5 required attribute will also provide client-side validation."
		)));
		$form->addElement(new Element\Textbox("URL", "url"));
		$form->addElement(new Element\YesNo("Visible", "visible"));
		$form->addElement(new Element\Select("Parent", "parent", $items));
		$form->addElement(new Element\Button($this->_('Submit', 'default')));
		$form->addElement(new Element\Button($this->_('Cancel', 'default'), "button", array(
			"onclick" => "history.go(-1);"
		)));

		return $form;
	}

	protected function getItem($id)
	{
		return $this->repo->find((int) $id /*item id*/);
	}
}
?>