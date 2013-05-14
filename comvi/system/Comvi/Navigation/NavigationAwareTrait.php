<?php
namespace Comvi\Navigation;

/**
 * Declare Navigation Aware trait.
 *
 * @package		Comvi.Navigation
 */
trait NavigationAwareTrait
{
	protected $navigation;

	public function setNavigation(Navigation $navigation)
	{
		$this->navigation = $navigation;
	}
}
