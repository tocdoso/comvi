<?php
namespace Comvi\Core;

/**
 * Declare Service Manager Aware trait.
 *
 * @package		Comvi.Core
 */
trait ServiceManagerAwareTrait
{
	protected $service_manager;

	public function setServiceManager(ServiceManagerInterface $service_manager)
	{
		$this->service_manager = $service_manager;
	}
}
