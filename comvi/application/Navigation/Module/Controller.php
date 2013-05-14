<?php
namespace Navigation\Module;
use Comvi\Core\Exception\HttpNotFoundException;
use Comvi\Core\AbstractController;
use Comvi\Core\Response;
use Comvi\Core\SessionManagerAwareInterface;
use Comvi\Core\SessionManagerAwareTrait;
use Comvi\Core\PaginationHelperTrait;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\DocumentAwareInterface;
use Comvi\Core\DocumentAwareTrait;
use Comvi\Core\CurrentURLAwareInterface;
use Comvi\Core\CurrentURLAwareTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;
use Comvi\ORM\EntityManagerAwareInterface;
use Comvi\ORM\EntityManagerAwareTrait;

use PFBC\Form;
use PFBC\Element;
use PFBC\Validation;
use PFBC\View;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Entity\Module;

/**
 * Menu Item Controller
 */
class Controller extends AbstractController implements SessionManagerAwareInterface, URLHelperInterface, CurrentURLAwareInterface, RouterAwareInterface, TranslatorAwareInterface, DocumentAwareInterface, EntityManagerAwareInterface
{
	use SessionManagerAwareTrait;
	use PaginationHelperTrait;
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
		$this->repo		 = $this->em->getRepository('Entity\\Module');
		$this->item_repo = $this->em->getRepository('Entity\\Item');
		$this->addTranslationModule('Navigation');
	}

	public function getEdit($id = null)
	{
		if ($id == null) {
			return $this->getEditList($this->getParam('item'));
		}

		$module = $this->getModule($id);
		if ($module === null) {
			throw new HttpNotFoundException();
		}
		$this->id = $id;
		$this->title = '#'.$module->getId();

		$form = $this->getForm();
		$form->setValues($module->toArray());
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Module', 'Navigation').' '.$this->title);
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function getEditList($item = null)
	{
		$modules = $this->getModules($item);
		$pagination = $this->getPagination(count($modules));

		$view = $this->getView('list_edit');
		$view->assign('title', $this->_('Edit modules', 'Navigation'));
		$view->assign('modules', $modules);
		$view->assign('pagination', $pagination->parse(), false);
		$view->assign('item', $item);

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
					'name'	=> $this->_('Modules', 'Navigation')
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
					'name'	=> $this->_('Modules', 'Navigation'),
					'url'	=> '?controller=Navigation/Module/Controller&task=Edit'
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
		$redirect = '?controller=Navigation/Module/Controller&task=Edit&id='.$id;

		if (Form::isValid('editModule')) {
			$item = $this->getItem($_POST['item']);
			$item_module = $this->getItem($_POST['module']);
			$module = $this->getModule($id);
			$module->fromArray($_POST);
			$module->setItem($item);
			$module->setModule($item_module);
			$this->em->flush();
			//$redirect = '?controller=Navigation/Module/Controller&task=Edit';
		}

		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->build($this->url($redirect)));

		return $response;
	}

	public function getAdd()
	{
		$module = new Module;
		$values =  array('item' => $this->getParam('item')) + $module->toArray();
		$form = $this->getForm();
		$form->setValues($values);
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Add module', 'Navigation'));
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
				'name'	=> $this->_('Module', 'Navigation'),
				'url'	=> '?controller=Navigation/Module/Controller'
			),
			array(
				'name'	=> $this->_('Add', 'default')
			)
		);

		return $response;
	}

	public function postAdd()
	{
		$redirect = '?controller=Navigation/Module/Controller&task=Add';

		if (Form::isValid('editModule')) {
			$item = $this->getParam('item');
			$item = $item ? $this->getItem($item) : null;
			$item_module = $this->getItem($_POST['module']);
			$module = new Module;
			$module->fromArray($_POST);
			$module->setItem($item);
			$module->setModule($item_module);
			$this->em->persist($module);
			$this->em->flush();
			$redirect = '?controller=Navigation/Module/Controller&task=Edit';
		}

		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->build($this->url($redirect)));

		return $response;
	}

	protected function getForm()
	{
		$items = $this->item_repo->childrenFlat();
		$items = array('' => '&lt;root&gt;') + $items;
		$modules = $items;

		$this->document->addStyle('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css');
		$static_url = $this->document->static_url;
		$form = new Form("editModule");
		$form->configure(array(
			"action" => $this->build($this->current_url),
			"prevent" => array('bootstrap', 'jQuery'),
			"resourcesPath" => substr($static_url, 0, -1)
		));
		$form->addElement(new Element\Select("Item", "item", $items));
		$form->addElement(new Element\Select("Module", "module", $modules, array(
			"required" => 1
		)));
		$form->addElement(new Element\Select("Assign", "assign", Module::getAssignChoices()));
		$form->addElement(new Element\YesNo("Enable", "enable"));
		$form->addElement(new Element\Textbox("Position", "position", array(
			"required" => 1
		)));
		$form->addElement(new Element\Button($this->_('Submit', 'default')));
		$form->addElement(new Element\Button($this->_('Cancel', 'default'), "button", array(
			"onclick" => "history.go(-1);"
		)));

		return $form;
	}

	protected function getItem($id)
	{
		return $this->item_repo->find((int) $id /*item id*/);
	}

	protected function getModule($id)
	{
		return $this->repo->find((int) $id /*module id*/);
	}

	protected function getModules($item = null)
	{
		$where = '';
		if ($item !== null) {
			$where = is_numeric($item) ? ' WHERE i.id = '.(int) $item : ' WHERE m.item is NULL';
		}
		$first_result = ($this->getPage() - 1) * $this->getLimit();
		$dql = 'SELECT m FROM Entity\\Module m LEFT JOIN m.item i'.$where;
		$query = $this->em->createQuery($dql)
			->setFirstResult($first_result)
			->setMaxResults($this->getLimit());

		return new Paginator($query, $fetchJoinCollection = false);
	}
}
?>