<?php
namespace Comvi\Core;

/**
 * Declare Service Manager interface.
 *
 * @package		Comvi.Core
 */
interface ServiceManagerInterface
{
    public function get($name);
	public function set($name, $service);
}
