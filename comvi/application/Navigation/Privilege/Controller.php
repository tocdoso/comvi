<?php
namespace Navigation\Privilege;
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
use Entity\Privilege;

/**
 * Navigation Privilege Controller
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
		$this->repo		 = $this->em->getRepository('Entity\\Privilege');
		$this->role_repo = $this->em->getRepository('Entity\\Role');
		$this->item_repo = $this->em->getRepository('Entity\\Item');
		$this->addTranslationModule('Navigation');
	}

	public function getEdit($id = null)
	{
		if ($id == null) {
			return $this->getEditList($this->getParam('item'));
		}

		$privilege = $this->getPrivilege($id);
		if ($privilege === null) {
			throw new HttpNotFoundException();
		}

		$this->id = $id;
		$this->title = '#'.$privilege->getId();

		$form = $this->getForm();
		$form->setValues($privilege->toArray());
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Privilege', 'Navigation').' '.$this->title);
		$view->assign('form', $form->render(true), false);

		return $view;
	}


	public function getEditList($item = null)
	{
		$privileges = $this->getPrivileges($item);
		$pagination = $this->getPagination(count($privileges));

		$view = $this->getView('list_edit');
		$view->assign('title', $this->_('Edit privileges', 'Navigation'));
		$view->assign('privileges', $privileges);;
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
					'name'	=> $this->_('Privileges', 'Navigation')
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
					'name'	=> $this->_('Privileges', 'Navigation'),
					'url'	=> '?controller=Navigation/Privilege/Controller&task=Edit'
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
		if (Form::isValid('editPrivilege')) {
			$role = $this->getRole($_POST['role']);
			$item = $this->getItem($_POST['item']);
			$privilege = $this->getPrivilege($id);
			$privilege->fromArray($_POST);
			$privilege->setRole($role);
			$privilege->setItem($item);
			$this->em->flush();
		}
		//else {
			$response = new Response;
			$response
				->setStatus(302)
				->setHeader('Location', $this->build($this->url('?controller=Navigation/Privilege/Controller&task=Edit&id='.$id)));

			return $response;
		//}
	}

	public function getAdd()
	{
		$form = $this->getForm();
		$privilege = new Privilege;
		$form->setValues($privilege->toArray());
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Add privilege', 'Navigation'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function postAdd()
	{
		if (Form::isValid('editPrivilege')) {
			$role = $this->getRole($_POST['role']);
			$item = $this->getItem($_POST['item']);
			$privilege = new Privilege;
			$privilege->fromArray($_POST);
			$privilege->setRole($role);
			$privilege->setItem($item);
			$this->em->persist($privilege);
			$this->em->flush();
		}
		//else {
			$response = new Response;
			$response
				->setStatus(302)
				->setHeader('Location', $this->build($this->url('?controller=Navigation/Privilege/Controller&task=Add')));

			return $response;
		//}
	}

	public function afterGetAdd($response)
	{
		$this->breadcrumbs = array(
			array(
				'name'	=> $this->_('Navigation', 'Navigation'),
				'url'	=> '?controller=Navigation/Controller'
			),
			array(
				'name'	=> $this->_('Privilege', 'Navigation'),
				'url'	=> '?controller=Navigation/Privilege/Controller'
			),
			array(
				'name'	=> $this->_('Add', 'default')
			)
		);

		return $response;
	}

	protected function getForm()
	{
		$items = $this->item_repo->childrenFlat();
		$items = array('' => '&lt;null&gt;') + $items;

		$roles = $this->role_repo->childrenFlat();
		$roles = array('' => '&lt;null&gt;') + $roles;

		$this->document->addStyle('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css');
		$static_url = $this->document->static_url;
		$form = new Form("editPrivilege");
		$form->configure(array(
			"action" => $this->build($this->current_url),
			"prevent" => array('bootstrap', 'jQuery'),
			"resourcesPath" => substr($static_url, 0, -1)
		));
		$form->addElement(new Element\Select("Item", "item", $items));
		$form->addElement(new Element\Select("Role", "role", $roles));
		$form->addElement(new Element\Textbox("Task", "task"));
		$form->addElement(new Element\YesNo("Allow", "allow"));
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

	protected function getRole($id)
	{
		return $this->role_repo->find((int) $id /*role id*/);
	}

	protected function getPrivilege($id)
	{
		return $this->repo->find((int) $id /*privilege id*/);
	}

	protected function getPrivileges($item = null)
	{
		$where = '';
		if ($item !== null) {
			$where = is_numeric($item) ? ' WHERE i.id = '.(int) $item : ' WHERE m.item is NULL';
		}
		$first_result = ($this->getPage() - 1) * $this->getLimit();
		$dql = 'SELECT p FROM Entity\\Privilege p LEFT JOIN p.role r LEFT JOIN p.item i'.$where;
		$query = $this->em->createQuery($dql)
			->setFirstResult($first_result)
			->setMaxResults($this->getLimit());

		return new Paginator($query, $fetchJoinCollection = false);
	}
}
?>