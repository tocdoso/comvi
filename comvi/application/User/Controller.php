<?php
namespace User;
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
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;
use Comvi\Core\CurrentURLAwareInterface;
use Comvi\Core\CurrentURLAwareTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\ORM\EntityManagerAwareInterface;
use Comvi\ORM\EntityManagerAwareTrait;

use PFBC\Form;
use PFBC\Element;
use PFBC\Validation;
use PFBC\View;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Entity\User;

/**
 * User Controller
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
		$this->repo		 = $this->em->getRepository('Entity\\User');
		$this->role_repo = $this->em->getRepository('Entity\\Role');
		$this->addTranslationModule();
	}

	public function getIndex($id = null)
	{
		if ($id == null) {
			return $this->getList();
		}

		$user = $this->getUser($id);
		if ($user === null) {
			throw new HttpNotFoundException;
		}
		$this->id = $id;
		$this->name = '#'.$user->getId();

		$view = $this->getView();
		$view->assign('title', $this->_('User').' '.$this->name);
		$view->assign('user', $user);

		return $view;
	}

	public function getList()
	{
		$users = $this->getUsers();
		$pagination = $this->getPagination(count($users));

		$view = $this->getView('list');
		$view->assign('title', $this->_('List users'));
		$view->assign('users', $users);
		$view->assign('pagination', $pagination->parse(), false);

		return $view;
	}

	public function afterGetIndex($response)
	{
		if (!isset($this->id)) {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('User')
				)
			);
		}
		else {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('User'),
					'url'	=> '?controller=User/Controller'
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
		$view->assign('title', $this->_('Add user'));
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function afterGetAdd($response)
	{
		$this->breadcrumbs = array(
			array(
				'name'	=> $this->_('User'),
				'url'	=> '?controller=User/Controller&task=Edit'
			),
			array(
				'name'	=> $this->_('Add', 'default')
			)
		);

		return $response;
	}

	public function postAdd()
	{
		$redirect = '?controller=User/Controller&task=Add';

		if (Form::isValid('editUser')) {
			$user = new User;
			$user->fromArray($_POST);
			if (isset($_POST['roles'])) {
				foreach ($_POST['roles'] as $role_id) {
					$role = $this->getRole($role_id);
					$user->getRoles()->add($role);
				}
			}
			$this->em->persist($user);
			$this->em->flush();
			$redirect = '?controller=User/Controller&task=Edit';
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

		$user = $this->getUser($id);
		if ($user === null) {
			throw new HttpNotFoundException;
		}

		$this->id = $id;
		$this->name = $user->getEmail();

		$values = $user->toArray();
		foreach ($user->getRoles() as $role) {
			$values['roles'][] = $role->getId();
		}

		$form = $this->getForm();
		$form->setValues($values);
		$view = $this->getView('edit');
		$view->assign('title', $this->_('User').' '.$this->name);
		$view->assign('form', $form->render(true), false);

		return $view;
	}

	public function getEditList()
	{
		$users = $this->getUsers();
		$pagination = $this->getPagination(count($users));

		$view = $this->getView('list_edit');
		$view->assign('title', $this->_('Edit users'));
		$view->assign('users', $users);
		$view->assign('pagination', $pagination->parse(), false);

		return $view;
	}

	public function afterGetEdit($response)
	{
		if (!isset($this->id)) {
			$this->breadcrumbs = array(
				array(
					'name'	=> $this->_('User', 'User')
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
					'name'	=> $this->name
				)
			);
		}

		return $response;
	}

	public function postEdit($id)
	{
		$redirect = '?controller=User/Controller&task=Edit&id='.$id;

		if (Form::isValid('editUser')) {
			$user = $this->getUser($id);
			$user->fromArray($_POST);
			foreach ($user->getRoles() as $role) {
				$user->getRoles()->removeElement($role);
			}
			if (isset($_POST['roles'])) {
				foreach ($_POST['roles'] as $role_id) {
					$role = $this->getRole($role_id);
					$user->getRoles()->add($role);
				}
			}
			$this->em->flush();
			$redirect = '?controller=User/Controller&task=Edit';
		}

		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->build($this->url()));

		return $response;
	}

	protected function getForm()
	{
		$roles = $this->role_repo->childrenFlat();

		$this->document->addStyle('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css');
		$static_url = $this->document->static_url;
		$form = new Form("editUser");
		$form->configure(array(
			"action" => $this->build($this->current_url),
			"prevent" => array('bootstrap', 'jQuery'),
			"resourcesPath" => substr($static_url, 0, -1)
		));
		$form->addElement(new Element\Email("Email", "email", array(
			"required" => 1
		)));
		$form->addElement(new Element\Checkbox("Roles", "roles", $roles));

		$form->addElement(new Element\Button($this->_('Submit', 'default')));
		$form->addElement(new Element\Button($this->_('Cancel', 'default'), "button", array(
			"onclick" => "history.go(-1);"
		)));

		return $form;
	}

	protected function getUsers()
	{
		$first_result = ($this->getPage() - 1) * $this->getLimit();
		$dql = 'SELECT u FROM Entity\\User u';
		$query = $this->em->createQuery($dql)
			->setFirstResult($first_result)
			->setMaxResults($this->getLimit());

		return new Paginator($query, $fetchJoinCollection = false);
	}

	protected function getUser($id)
	{
		return $this->repo->find((int) $id /*user id*/);
	}

	protected function getRole($id)
	{
		return $this->role_repo->find((int) $id /*role id*/);
	}

	/*protected function getUserByEmail($email)
	{
		return $this->repo->findOneBy(array('email' => $email);
	}*/
}
