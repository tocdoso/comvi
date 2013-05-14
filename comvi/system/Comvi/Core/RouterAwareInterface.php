<?php
namespace Comvi\Core;

/**
 * Declare Router Aware interface.
 *
 * @package		Comvi.Core
 */
interface RouterAwareInterface
{
	public function setRouter(RouterInterface $router);
}
