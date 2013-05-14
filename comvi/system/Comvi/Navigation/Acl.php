<?php
namespace Comvi\Navigation;
use Zend\Permissions\Acl\Acl as ZendAcl;

/**
 * Acl Class.
 *
 * @package		Comvi.Navigation
 */
class Acl extends ZendAcl
{
	protected $default_role = null;

	public function setDefaultRole($role)
	{
		$this->default_role = $role;
	}

    public function isDefaultRoleAllowed($resource = null, $privilege = null)
    {
		return parent::isAllowed($this->default_role, $resource, $privilege);
	}
}
