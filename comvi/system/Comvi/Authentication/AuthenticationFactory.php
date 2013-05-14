<?php
namespace Comvi\Authentication;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

/**
 * The AuthenticationFactory class.
 *
 * @package		Comvi.Authentication
 */
class AuthenticationFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$auth = new AuthenticationService;

		// Use 'someNamespace' instead of 'Zend_Auth'
		$auth->setStorage(new SessionStorage('Comvi_Authentication'));

		return $auth;	
	}
}
