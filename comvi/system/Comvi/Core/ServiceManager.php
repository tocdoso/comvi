<?php
namespace Comvi\Core;
use Zend\ServiceManager\ServiceManager as BaseServiceManager;

/**
 * Service Manager class.
 *
 * @package		Comvi.Core
 */
class ServiceManager extends BaseServiceManager implements ServiceManagerInterface
{
	public function set($name, $service)
	{
		return $this->setService($name, $service);
	}
}
