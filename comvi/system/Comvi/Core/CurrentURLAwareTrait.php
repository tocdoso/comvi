<?php
namespace Comvi\Core;

/**
 * Declare CurrentURL Aware trait.
 *
 * @package		Comvi.Core
 */
trait CurrentURLAwareTrait
{
	protected $current_url;

	public function setCurrentURL(URI $current_url)
	{
		$this->current_url = $current_url;
	}
}
