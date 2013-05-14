<?php
namespace Comvi\Core;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The PackageManagerFactory class.
 *
 * @package		Comvi.Core
 */
class PackageManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$package_manager = new PackageManager;
		$package_manager->setServiceManager($serviceLocator);

		return $package_manager;
	}
}
