<?php
namespace Comvi\Core;
use Comvi\Core\Helper\URL;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The CurrentURLFactory class.
 *
 * @package		Comvi.Core
 */
class CurrentURLFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$class = $serviceLocator->get('config')->class_map->uri;
		$current_url = new $class(URL::current());

		return $current_url;
	}
}
