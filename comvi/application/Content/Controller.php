<?php
namespace Content;
use Comvi\Core\Exception\HttpNotFoundException;
use Comvi\Core\AbstractController;
use Comvi\Core\Response;
use Comvi\Core\SessionManagerAwareInterface;
use Comvi\Core\SessionManagerAwareTrait;
use Comvi\Core\PaginationHelperTrait;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\CurrentURLAwareInterface;
use Comvi\Core\CurrentURLAwareTrait;
use Comvi\Core\ConfigAwareInterface;
use Comvi\Core\ConfigAwareTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;
use Comvi\Core\DocumentAwareInterface;
use Comvi\Core\DocumentAwareTrait;
use Comvi\ORM\EntityManagerAwareInterface;
use Comvi\ORM\EntityManagerAwareTrait;

use Comvi\Extend\Helper\HTML;

use PFBC\Form;
use PFBC\Element;
use PFBC\Validation;
use PFBC\View;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Entity\Content;
use Entity\Content\Category;
use Entity\Content\Tag;

/**
 * Content Controller
 */
class Controller extends AbstractController implements SessionManagerAwareInterface, URLHelperInterface, CurrentURLAwareInterface, ConfigAwareInterface, RouterAwareInterface, TranslatorAwareInterface, DocumentAwareInterface, EntityManagerAwareInterface
{
	use SessionManagerAwareTrait;
	use PaginationHelperTrait;
	use URLHelperTrait;
	use CurrentURLAwareTrait;
	use ConfigAwareTrait;
	use RouterAwareTrait;
	use TranslatorAwareTrait;
	use DocumentAwareTrait;
	use EntityManagerAwareTrait;

	/**
	* Class constructor
	*/
	public function init()
	{
		$this->repo	= $this->em->getRepository('Entity\\Content');
		$this->cat_repo = $this->em->getRepository('Entity\\Content\\Category');
		$this->tag_repo = $this->em->getRepository('Entity\\Content\\Tag');
		$this->addTranslationModule();
	}

	public function getIndex($name)
	{
		$result = $this->em
				->createQuery("SELECT ct FROM Entity\ContentTranslation ct WHERE ct.field = 'slug' AND ct.content = :content")
				->setParameter('content', $name)
				->getOneOrNullResult();

		$content = ($result === null) ? $this->getContentBySlug($name) : $this->getContent($result->getObject()->getId());
		if ($content === null) {
			throw new HttpNotFoundException();
		}
		//$this->id = $id;
		$this->content = $content;

		$view = $this->getView();
		$view->assign('title', $content->getTitle());
		$view->assign('description', $content->getDescription());
		$view->assign('body', $content->getBody(), false);

		return $view;
	}

	public function afterGetIndex($response)
	{
		$this->breadcrumbs = array(
			array(
				'name'	=> $this->_('Content'),
				'url'	=>	'?controller=Content/Controller'
			),
			array(
				'name'	=> $this->content->getTitle()
			)
		);

		if (isset($this->content)) {
			$query = $this->em
				->createQuery("SELECT c.slug FROM Entity\Content c WHERE c.id = :id")
				->setParameter('id', $this->content->getId());
			$default_lang = $this->config->i18n->default_language;
			$default_slug = $query->getSingleScalarResult();
			$this->reflangs = array($default_lang => $this->build($this->url('?controller=Content/Controller&name='.$default_slug.'&lang='.$default_lang)));

			$query = $this->em
				->createQuery("SELECT ct FROM Entity\ContentTranslation ct WHERE ct.object = :content AND ct.field = 'slug'")
				->setParameter('content', $this->content);
			$translations = $query->getResult();
			foreach ($translations as $translation) {
				$slug = $translation->getContent();//echo $slug;
				$lang = $translation->getLocale();
				$this->reflangs[$lang] = $this->build($this->url('?controller=Content/Controller&name='.$slug.'&lang='.$lang));
			}
		}

		$body = $this->content->getBody();
		$toc = (HTML::parseTOC($body));

		if ($toc !== null) {
			$view = $this->getView('box');
			$view->assign('title', 'Table of contents');
			$view->assign('content', $toc, false);
			$response->assign('toc', $view);
			$response->assign('body', $body, false);
		}

		return $response;
	}

	public function getAdd()
	{
		$content = new Content;
		$values =  array('category' => $this->getParam('category')) + $content->toArray();
		$form = $this->getForm();
		$form->setValues($values);
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Add content'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function afterGetAdd($response)
	{
		$this->breadcrumbs = array(
			array(
				'name'	=> $this->_('Content'),
				'url'	=> '?controller=Content/Controller&task=Edit'
			),
			array(
				'name'	=> $this->_('Add', 'default')
			)
		);

		return $response;
	}

	public function postAdd()
	{
		$redirect = '?controller=Content/Controller&task=Add';

		if (Form::isValid('editContent')) {
			$category = $this->getCategory($_POST['category']);
			$content = new Content;
			$content->fromArray($_POST);
			$content->setCategory($category);
			$this->em->persist($content);
			$this->em->flush();
			$redirect = '?controller=Content/Controller&task=Edit';
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
			return $this->getEditList($this->getParam('category'));
		}

		$content = $this->getContent($id);
		if ($content === null) {
			throw new HttpNotFoundException();
		}
		$this->id = $id;
		$this->title = $content->getTitle();

		$form = $this->getForm();
		$form->setValues($content->toArray());
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Edit content'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function getEditList($category = null)
	{
		$contents = $this->getContents($category);
		$pagination = $this->getPagination(count($contents));
		//$category = $this->getParam('category') ? $this->getCategory($this->getParam('category')) : null;
		$view = $this->getView('list_edit');
		//$view->assign('title', $category ? $category->getName() : $this->_('Edit contents'));
		$view->assign('title', $this->_('Edit contents'));
		$view->assign('contents', $contents);
		$view->assign('pagination', $pagination->parse(), false);
		$view->assign('category', $category);

		return $view;
	}

	public function afterGetEdit($response)
	{
		if (!isset($this->id)) {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('Content')
				)
			);
		}
		else {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('Content'),
					'url'	=> '?controller=Content/Controller&task=Edit'
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
		$redirect = '?controller=Content/Controller&task=Edit&id='.$id;

		if (Form::isValid('editContent')) {
			$category = $this->getCategory($_POST['category']);
			$content = $this->getContent($id);
			$content->fromArray($_POST);
			$content->setCategory($category);
			$this->em->flush();
			$redirect = '?controller=Content/Controller&task=Edit';
		}

		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->build($this->url($redirect)));

		return $response;
	}

	public function getDelete($id)
	{
		$content = $this->getContent($id);
		if ($content === null) {
			throw new HttpNotFoundException();
		}

		$this->em->remove($content);
		$this->em->flush();

		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->build($this->url('?controller=Content/Controller&task=Edit')));

		return $response;
	}

	protected function getForm($ignore = null)
	{
		$categories = $this->cat_repo->childrenFlat();
		$categories = array('' => '&lt;root&gt;') + $categories;

		$this->document->addStyle('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css');
		$static_url = $this->document->static_url;
		$form = new Form("editContent");
		$form->configure(array(
			"action" => $this->build($this->current_url),
			"prevent" => array('bootstrap', 'jQuery'),
			"resourcesPath" => substr($static_url, 0, -1)
		));
		$form->addElement(new Element\Textbox("Title", "title", array(
			"required" => 1,
			"longDesc" => "The required property provides a shortcut for applying the Required class to the element's
			validation property.  If supported, the HTML5 required attribute will also provide client-side validation."
		)));
		$form->addElement(new Element\Textbox("Slug", "slug", array(
			"validation" => new Validation\AlphaNumeric,
			"longDesc" => "The AlphaNumeric validation class will verify that the element's submitted value contains only letters, 
			numbers, underscores, and/or hyphens."
		)));
		$form->addElement(new Element\Textarea("Description", "description"));
		$form->addElement(new Element\TinyMCE("Body", "body", array(
			"required" => 1
		)));
		$form->addElement(new Element\Select("Status", "status", Content::getStatusChoices()));
		$form->addElement(new Element\YesNo("Featured", "featured"));
		$form->addElement(new Element\Select("Category", "category", $categories));
		$form->addElement(new Element\Button($this->_('Submit', 'default')));
		$form->addElement(new Element\Button($this->_('Cancel', 'default'), "button", array(
			"onclick" => "history.go(-1);"
		)));

		return $form;
	}

	protected function getCategory($id)
	{
		return $this->cat_repo->find((int) $id /*category id*/);
	}

	protected function getContent($id)
	{
		return $this->repo->find((int) $id /*content id*/);
	}

	protected function getContentBySlug($slug)
	{
		return $this->repo->findOneBy(array('slug' => $slug) /*content slug*/);
	}

	protected function getContents($category = null)
	{
		$where = '';
		if ($category !== null) {
			$where = is_numeric($category) ? ' WHERE cat.id = '.(int) $category : ' WHERE m.category is NULL';
		}
		$first_result = ($this->getPage() - 1) * $this->getLimit();
		$dql = 'SELECT c FROM Entity\\Content c LEFT JOIN c.category cat'.$where;
		$query = $this->em->createQuery($dql)
			->setFirstResult($first_result)
			->setMaxResults($this->getLimit());

		return new Paginator($query, $fetchJoinCollection = false);
	}
}
?>