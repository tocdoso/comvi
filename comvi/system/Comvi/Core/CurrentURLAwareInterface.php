<?php
namespace Comvi\Core;

/**
 * Declare CurrentURL Aware interface.
 *
 * @package		Comvi.Core
 */
interface CurrentURLAwareInterface
{
	public function setCurrentURL(URI $current_url);
}
