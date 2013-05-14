<?php
namespace Comvi\Core;

/**
 * Declare Config Aware interface.
 *
 * @package		Comvi.Core
 */
interface ConfigAwareInterface
{
	public function setConfig(Config $config);
}
