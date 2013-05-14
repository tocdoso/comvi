<?php
namespace Comvi\Navigation;

/**
 * Declare Navigation Aware interface.
 *
 * @package		Comvi.Navigation
 */
interface NavigationAwareInterface
{
	public function setNavigation(Navigation $navigation);
}
