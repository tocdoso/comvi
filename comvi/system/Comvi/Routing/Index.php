<?php
namespace Comvi\Routing;
use Comvi\Core\Loader\ClassLoader;

/**
 * Comvi/Routing bootstrap class.
 */
class Index extends \Comvi\Core\Package
{
	public function index()
	{
		$options = $this->get('config')->route->toArray();
		$router = new Router($options);
		$this->get('router')->addRouter($router);
	}
}
