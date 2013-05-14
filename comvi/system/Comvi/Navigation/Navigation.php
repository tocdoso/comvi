<?php
namespace Comvi\Navigation;
use Comvi\Core\URI;
use Zend\Session\Container as SessionContainer; 
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Entity\Item;

/**
 * Navigation Class.
 *
 * @package		Comvi.Navigation
 */
class Navigation
{
	protected $item_repo;
	protected $current_url;
	//protected $home_url;
	//protected $home;
	protected $active_item;
	protected $acl;
	protected $modules = array();

	public function __construct($em, $current_url/*, $home_url, $home_name*/)
	{
		$this->item_repo = $em->getRepository('Entity\\Item');
		$this->module_repo = $em->getRepository('Entity\\Module');
		$this->role_repo = $em->getRepository('Entity\\Role');
		$this->privilege_repo = $em->getRepository('Entity\\Privilege');
		//$this->item_repo->setChildrenIndex('children');
		$this->current_url = $current_url;
		/*$this->home_url = $home_url;
		$this->home_name = $home_name;

		$this->home = new Item;
		$this->home->setName($home_name);
		$this->home->setURL($home_url->toString());*/

		if (($this->active_item = $this->item_repo->findURL($this->current_url)) !== null) {
			$this->active_item->active();
		}

		// init ACL
		$this->initACL();
	}

	protected function initACL()
	{
		$this->acl = new Acl;
		// add roles
		foreach ($this->role_repo->getChildren() as $child) {
			$name = $child->getName();
			$parent = $child->getParent() ? $child->getParent()->getName() : null;
			$this->acl->addRole(new Role($name), $parent);
		}
		if (!$this->acl->hasRole('Un-logged')) {
			$this->acl->addRole('Un-logged');
		}
		if (!$this->acl->hasRole('Logged')) {
			$this->acl->addRole('Logged');
		}
		// set default role
		$role = null;
		$user = new SessionContainer('user');
		if ($user->id) {
			$role = new Role($user->id);
			$this->acl->addRole($role, $user->roles);
		}
		elseif ($user->email) {
			$role = new Role('Logged');
		}
		else {
			$role = new Role('Un-logged');
		}
		$this->acl->setDefaultRole($role);

		// add rules for all roles
		$this->addPrivileges($this->privilege_repo->findBy(array('item' => null)));

		// add resources & rules
		foreach ($this->item_repo->getChildren() as $child) {
			$resource	= new Resource($child->getId());
			$parent		= $child->getParent() ? $child->getParent()->getId() : null;

			$this->acl->addResource($resource, $parent);
			$this->addPrivileges($child->getPrivileges(), $resource);
		}
	}

	public function getACL()
	{
		return $this->acl;
	}

	public function addbreadcrumbs($breadcrumbs)
	{
		if (isset($this->active_item)) {
			return;
		}

		foreach ($breadcrumbs as $i => $breadcrumb) {
			$item = new Item;
			$item->fromArray($breadcrumb);

			if (isset($this->active_item)) {
				$item->setParent($this->active_item);
			}

			$this->active_item = $item;
		}

		$item = $this->active_item;
		while ($item->getParent() != null) {
			if ($item->getParent()->getURL() === null) {
				continue;
			}

			$item_url = new URI($item->getParent()->getURL());

			if (($found = $this->item_repo->findURL($item_url)) !== null) {
				$item->setParent($found);
				break;
			}

			$item = $item->getParent();
		};

		$this->active_item->active();
	}

	public function getChildren($item = null)
	{
		$item = ($item !== null) ? $this->getItem($item) : null;
		return $this->item_repo->getChildren($item, true);
	}

	public function getBreadcrumbs($prepend_home_item = false)
	{
		if (!isset($this->active_item)/* || $this->current_url->equal($this->home_url)*/) {
			return array();
		}

		$active_item = clone $this->active_item;
		$active_item->setURL(null);
		$items = $active_item->getAncestors(true);

		/*if ($prepend_home_item === true) {
			array_unshift($items, $this->home);
		}*/

		return $items;
	}

	public function getModules()
	{
		static $added_modules = false;

		if ($added_modules === false) {
			$root_modules = $this->module_repo->findBy(array('item' => null));

			if ($this->active_item) {
				$this->addModules($root_modules, array('children', 'all'));

				foreach($this->active_item->getAncestors() as $ancestor) {
					$this->addModules($ancestor->getModules(), array('children', 'all'));
				}

				$this->addModules($this->active_item->getModules(), array('this', 'all'));
			}
			else {
				$this->addModules($root_modules, array('this', 'all'));
			}

			$added_modules = true;
		}

		return $this->modules;
	}

	protected function addModules($modules, $assigns = array('all'))
	{
		foreach ($modules as $module) {
			if (in_array($module->getAssign(), $assigns)) {
				$resource	= $module->getModule()->getId();
				$uri		= new URI($module->getModule()->getURL());
				$task		= $uri->getVar('task', 'Index');

				if ($this->acl->isDefaultRoleAllowed($resource, $task)) {
					$hash = md5($module->getModule()->getId().$module->getPosition());

					if ($module->isEnable() && !isset($this->modules[$hash])) {
						$this->modules[$hash] = $module;
					}
					elseif (!$module->isEnable() && isset($this->modules[$hash])) {
						unset($this->modules[$hash]);
					}
				}
			}
		}
	}

	protected function addPrivileges($privileges, $resource = null)
	{
		foreach ($privileges as $privilege) {
			$task = $privilege->getTask() ? $privilege->getTask() : null;
			if ($privilege->isAllowed()) {
				$this->acl->allow($privilege->getRole(), $resource, $task);
			}
			else {
				$this->acl->deny($privilege->getRole(), $resource, $task);
			}
		}
	}

	public function getItem($id)
	{
		return $this->item_repo->find((int) $id /*item id*/);
	}

	public function getActiveItem()
	{
		return $this->active_item;
	}
}
?>