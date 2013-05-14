<?php
namespace Comvi\Core;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The RequestFactory class.
 *
 * @package		Comvi.Core
 */
class RequestFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$uri = $serviceLocator->get('current_url');
		$method = Request::getMethod();

		$class = $serviceLocator->get('config')->class_map->request;
		$request = new $class($uri, $method, true);
		$request->setServiceManager($serviceLocator);

		return $request;
	}
}
