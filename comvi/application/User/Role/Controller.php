<?php
namespace User\Role;
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
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\Core\DocumentAwareInterface;
use Comvi\Core\DocumentAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;
use Comvi\ORM\EntityManagerAwareInterface;
use Comvi\ORM\EntityManagerAwareTrait;

use PFBC\Form;
use PFBC\Element;
use PFBC\Validation;
use PFBC\View;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Entity\Role;

/**
 * User Role Controller
 */
class Controller extends AbstractController implements SessionManagerAwareInterface, URLHelperInterface, CurrentURLAwareInterface, RouterAwareInterface, DocumentAwareInterface, TranslatorAwareInterface, EntityManagerAwareInterface
{
	use SessionManagerAwareTrait;
	use PaginationHelperTrait;
	use URLHelperTrait;
	use CurrentURLAwareTrait;
	use RouterAwareTrait;
	use DocumentAwareTrait;
	use TranslatorAwareTrait;
	use EntityManagerAwareTrait;

	public function init()
	{
		$this->repo	= $this->em->getRepository('Entity\\Role');
		$this->addTranslationModule('User');
	}

	public function getIndex($id = null)
	{
		if ($id == null) {
			return $this->getList();
		}

		$role = $this->getRole($id);
		if ($role === null) {
			throw new HttpNotFoundException;
		}
		$this->id = $id;
		$this->name = $role->getName();

		$view = $this->getView();
		$view->assign('title', $role->getName());
		$view->assign('role', $role);

		return $view;
	}

	public function getList()
	{
		$roles = $this->getRoles();
		$pagination = $this->getPagination(count($roles));

		$view = $this->getView('list');
		$view->assign('title', $this->_('List roles', 'User'));
		$view->assign('roles', $roles);
		$view->assign('pagination', $pagination->parse(), false);

		return $view;
	}

	public function afterGetIndex($response)
	{
		if (!isset($this->id)) {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('User', 'User'),
					'url'	=> '?controller=User/Controller'
				),
				array(
					'name'	=> $this->_('Role', 'User')
				)
			);
		}
		else {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('User', 'User'),
					'url'	=> '?controller=User/Controller'
				),
				array(
					'name'	=> $this->_('Role', 'User'),
					'url'	=> '?controller=User/Role/Controller'
				),
				array(
					'name'	=> $this->name
				)
			);
		}

		return $response;
	}

	public function getAdd()
	{
		$form = $this->getForm();
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Add role', 'User'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function afterGetAdd($response)
	{
		$this->breadcrumbs = array(
			array(
				'name'	=> $this->_('User', 'User'),
				'url'	=> '?controller=User/Controller&task=Edit'
			),
			array(
				'name'	=> $this->_('Role', 'User'),
				'url'	=> '?controller=User/Role/Controller&task=Edit'
			),
			array(
				'name'	=> $this->_('Add', 'default')
			)
		);

		return $response;
	}

	public function postAdd()
	{
		$redirect = '?controller=User/Role/Controller&task=Add';

		if (Form::isValid('editRole')) {
			$parent = $this->getRole($_POST['parent']);
			$role = new Role;
			$role->fromArray($_POST);
			$role->setParent($parent);
			$this->em->persist($role);
			$this->em->flush();
			$redirect = '?controller=User/Role/Controller&task=Edit';
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

		$role = $this->getRole($id);
		if ($role === null) {
			throw new HttpNotFoundException;
		}
		$this->id = $id;
		$this->name = $role->getName();

		$form = $this->getForm($role);
		$form->setValues($role->toArray());
		$view = $this->getView('edit');
		$view->assign('title', $this->_('Edit role', 'User'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function getEditList()
	{
		$view = $this->getView('list_edit');
		$view->assign('title', $this->_('Edit roles', 'User'));
		$view->assign('roles', $this->repo->childrenHierarchy());

		return $view;
	}

	public function afterGetEdit($response)
	{
		if (!isset($this->id)) {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('User', 'User'),
					'url'	=> '?controller=User/Controller&task=Edit'
				),
				array(
					'name'	=> $this->_('Role', 'User')
				)
			);
		}
		else {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('User', 'User'),
					'url'	=> '?controller=User/Controller&task=Edit'
				),
				array(
					'name'	=> $this->_('Role', 'User'),
					'url'	=> '?controller=User/Role/Controller&task=Edit'
				),
				array(
					'name'	=> $this->name
				)
			);
		}

		return $response;
	}

	public function postEdit($id)
	{
		$redirect = '?controller=User/Role/Controller&task=Edit&id='.$id;

		if (Form::isValid('editRole')) {
			$parent = $this->getRole($_POST['parent']);
			$role = $this->getRole($id);
			$role->fromArray($_POST);
			$role->setParent($parent);
			$this->em->flush();
			$redirect = '?controller=User/Role/Controller&task=Edit';
		}

		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->build($this->url($redirect)));

		return $response;
	}

	protected function getForm($ignore = null)
	{
		$roles = $this->repo->childrenFlat();
		$roles = array('' => '&lt;root&gt;') + $roles;

		if ($ignore !== null) {
			unset($roles[$ignore->getId()]);
			foreach ($this->repo->children($ignore) as $child) {
				unset($roles[$child->getId()]);
			}
		}

		$this->document->addStyle('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css');
		$static_url = $this->document->static_url;
		$form = new Form("editRole");
		$form->configure(array(
			"action" => $this->build($this->current_url),
			"prevent" => array('bootstrap', 'jQuery'),
			"resourcesPath" => substr($static_url, 0, -1)
		));
		$form->addElement(new Element\Textbox("Name", "name", array(
			"required" => 1
		)));
		$form->addElement(new Element\Select("Parent", "parent", $roles));
		$form->addElement(new Element\Button($this->_('Submit', 'default')));
		$form->addElement(new Element\Button($this->_('Cancel', 'default'), "button", array(
			"onclick" => "history.go(-1);"
		)));

		return $form;
	}

	protected function getRole($id)
	{
		return $this->repo->find((int) $id /*role id*/);
	}

	protected function getRoles()
	{
		$first_result = ($this->getPage() - 1) * $this->getLimit();
		$dql = 'SELECT r FROM Entity\\Role r';
		$query = $this->em->createQuery($dql)
			->setFirstResult($first_result)
			->setMaxResults($this->getLimit());

		return new Paginator($query, $fetchJoinCollection = false);
	}
}
