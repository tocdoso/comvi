<?php
namespace Comvi\Core;

/**
 * Declare Resources Provider Aware interface.
 *
 * @package		Comvi.Core
 */
interface ServiceManagerAwareInterface
{
	public function setServiceManager(ServiceManagerInterface $service_manager);
}
