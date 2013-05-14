<?php
namespace Comvi\Core;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The RouterFactory class.
 *
 * @package		Comvi.Core
 */
class RouterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		// Setup Basic Router
		$options = $serviceLocator->get('config')->route->toArray();
		$options['index_file'] = basename($_SERVER['SCRIPT_NAME']);
		$router = new Router($options);

		$class = $serviceLocator->get('config')->class_map->router;
		$routers = new $class;
		$routers->addRouter($router);

		return $routers;
	}
}
