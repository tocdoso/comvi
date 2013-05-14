<?php
namespace Comvi\Permissions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;

/**
 * The PermissionsFactory class.
 *
 * @package		Comvi.Core
 */
class PermissionsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$acl = new Permissions;
$acl = new Acl();

$roleGuest = new Role('guest');
$acl->addRole($roleGuest);
$acl->addRole(new Role('staff'), $roleGuest);
$acl->addRole(new Role('editor'), 'staff');
$acl->addRole(new Role('administrator'));

// Guest may only view content
$acl->allow($roleGuest, null, 'view');

/*
Alternatively, the above could be written:
$acl->allow('guest', null, 'view');
*/

// Staff inherits view privilege from guest, but also needs additional
// privileges
$acl->allow('staff', null, array('edit', 'submit', 'revise'));

// Editor inherits view, edit, submit, and revise privileges from
// staff, but also needs additional privileges
$acl->allow('editor', null, array('publish', 'archive', 'delete'));

// Administrator inherits nothing, but is allowed all privileges
$acl->allow('administrator');
	}
}
