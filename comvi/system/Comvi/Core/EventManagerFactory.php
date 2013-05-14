<?php
namespace Comvi\Core;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\EventManager;

/**
 * The EventManagerFactory class.
 *
 * @package		Comvi.Core
 */
class EventManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$events = new EventManager;
        /*$events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));*/

        return $events;
	}
}
