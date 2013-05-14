<?php
namespace Comvi\Core;

/**
 * The Package class.
 *
 * @package		Comvi.Core
 */
abstract class Package implements ServiceManagerAwareInterface
{
	use ServiceManagerAwareTrait;

    public function get($name)
	{
		return $this->service_manager->get($name);
	}

	public function set($name, $service)
	{
		return $this->service_manager->set($name, $service);
	}

	public function index()
	{
	}
}
?>