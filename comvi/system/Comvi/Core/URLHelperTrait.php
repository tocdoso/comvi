<?php
namespace Comvi\Core;

/**
 * Declare URL Helper Trait.
 *
 * @package		Comvi.Core
 */
trait URLHelperTrait
{
	public function url($url)
	{
		return new URI($url);
	}
}
