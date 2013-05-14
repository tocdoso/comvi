<?php
namespace Comvi\Core;

/**
 * Class contain routers.
 *
 * @package		Comvi.Core
 */
class Routers implements RouterInterface
{
	protected $routers = array();

	public function __construct()
	{
	}

	public function addRouter(RouterInterface $router, $name = null/*, $prepend = false*/)
	{
		//if ($prepend === true) {
		//	array_unshift($this->routers, $router);
		//}
		//else {
		//	$this->routers[] = $router;
		//}

		if ($name === null) {
			$this->routers[] = $router;
		}
		else {
			$this->routers[$name] = $router;
		}
	}

	public function getRouter($name)
	{
		return $this->routers[$name];
	}

	/**
	 * Parse the URI.
	 *
	 * @param	object	The URI
	 *
	 * @return	void
	 */
	public function parseAndCheck(URI &$uri)
	{
		foreach ($this->routers as $router) {
			if (method_exists($router, 'checkBeforeParse')) {
				$router->checkBeforeParse($uri);
			}

			$router->parse($uri);

			if (method_exists($router, 'checkAfterParse')) {
				$router->checkAfterParse($uri);
			}
		}
	}

	/**
	 * Parse the URI.
	 *
	 * @param	object	The URI
	 *
	 * @return	void
	 */
	public function parse(URI &$uri)
	{
		foreach ($this->routers as $router) {
			$router->parse($uri);
		}
	}

	/**
	 * Build the URI.
	 *
	 * @param	object	The URI
	 *
	 * @return	void
	 */
	public function build(URI &$uri)
	{
		foreach (array_reverse($this->routers) as $router) {
			$router->build($uri);
		}
		//print_r($uri);
	}
}
?>