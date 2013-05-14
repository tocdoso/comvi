<?php
namespace Comvi\Core;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The ProfilerFactory class.
 *
 * @package		Comvi.Core
 */
class ProfilerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$profiler = new Profiler;
		$profiler->mark($serviceLocator->get('config')->start_time, $serviceLocator->get('config')->start_mem);

		return $profiler;
	}
}
