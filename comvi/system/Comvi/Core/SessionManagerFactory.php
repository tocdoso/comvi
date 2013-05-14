<?php
namespace Comvi\Core;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Config\StandardConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;

/**
 * The SessionManagerFactory class.
 *
 * @package		Comvi.Core
 */
class SessionManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$config = new StandardConfig;
		$config->setOptions($serviceLocator->get('config')->session->toArray());
		$manager = new SessionManager($config);
		$manager->start();

		/**
		 * Optional: If you later want to use namespaces, you can already store the 
		 * Manager in the shared (static) Container (=namespace) field
		 */
		//Container::setDefaultManager($manager);

		return $manager;
	}
}
