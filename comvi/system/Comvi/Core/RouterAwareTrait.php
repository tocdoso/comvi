<?php
namespace Comvi\Core;

/**
 * Declare Router Aware trait.
 *
 * @package		Comvi.Core
 */
trait RouterAwareTrait
{
	protected $router;

	public function setRouter(RouterInterface $router)
	{
		$this->router = $router;
	}

	public function parse(URI $uri)
	{
		$u = clone $uri;
		$this->router->parse($u);
		return $u;
	}

	public function build(URI $uri)
	{
		$u = clone $uri;
		$this->router->build($u);
		return $u;
	}
}
