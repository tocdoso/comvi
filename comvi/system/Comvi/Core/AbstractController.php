<?php
namespace Comvi\Core;
use ReflectionClass;

/**
 * Abstract class for a Controller.
 *
 * Controller (controllers are where you put all the actual code) Provides basic
 * functionality, such as rendering views (aka displaying templates).
 *
 * @package		Comvi.Core
 */
abstract class AbstractController
{
	use DependencyInjectorTrait;

	/**
	 * The name of the controller
	 */
	protected $module;


	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 */
	public function __construct()
	{
		if (!isset($this->module)) {
			$reflector	= new ReflectionClass($this); // class Foo of namespace A
			$this->module	= $reflector->getNamespaceName();
			
			/*if ($reflector->getShortName() !== 'Controller') {
				$this->module .= '\\'.$reflector->getShortName();
			}*/
		}
	}

	public function before()
	{
	}

	public function after($response)
	{
		if (is_array($response)) {
			if ($this->getFormat() === 'json') {
				$response = json_encode($response);
			}
			else {
				$view = $this->getView();
				foreach ($response as $key => $value) {
					$view->assign($key, $value);
				}
				$response = $view;
			}
		}

		// Make sure the $response is a Response object
		/*if (!$response instanceof Response) {
			$response = new Response($response);
		}*/

		return $response;
	}

	/**
	 * Authorisation check
	 *
	 * @return	boolean	True if authorised
	 */
	protected function authorise()
	{
		return true;
	}

	protected function get($resource)
	{
		return $this->$resource;
	}

	/**
	 * Method to get a view object, loading it if required.
	 *
	 * @param	string	The view layout. Optional.
	 * @return	View	The view.
	 */
	protected function getView($layout = null)
	{
		if ($layout === null) {
			$layout = $this->getLayout();
		}

		$options = array(
			'module'		=> $this->module,
			'layout'		=> $layout,
			'layout_ext'	=> '.'.$this->getFormat().'.php'
		);

		return $this->newInstance($this->getViewClass(), $options);
	}

	protected function getViewClass()
	{
		return $this->module.'\\View';
	}

	protected function getLayout()
	{
		return $this->getParam('layout', 'index');
	}

	protected function getFormat()
	{
		return $this->getParam('format', 'html');
	}

	protected function getParam($name, $default = null)
	{
		return isset($this->params[$name]) ? $this->params[$name] : $default;
	}
}
?>