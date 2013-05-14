<?php
namespace Comvi\Core;

/**
 * Declare Config Aware trait.
 *
 * @package		Comvi.Core
 */
trait ConfigAwareTrait
{
	protected $config;

	public function setConfig(Config $config)
	{
		$this->config = $config;
	}
}
