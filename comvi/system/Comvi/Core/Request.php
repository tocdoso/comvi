<?php
namespace Comvi\Core;
use Comvi\Core\Exception\HttpNotFoundException;
use Zend\EventManager\EventManager;
use ReflectionClass;

/**
 * The Request class is used to create and manage requests.
 *
 * @package		Comvi.Core
 */
class Request implements ServiceManagerAwareInterface
{
	use DependencyInjectorTrait;
	use ServiceManagerAwareTrait;


	public static function getMethod()
	{
		return strtolower($_SERVER['REQUEST_METHOD']);
	}


	/**
	 * The Request's URI object.
	 *
	 * @var  Uri
	 */
	protected $uri;

	/**
	 * @var  string  $method  request method
	 */
	protected $method;

	/**
	 * @var  string
	 */
	protected $is_main;

	/**
	 * Controller instance once instantiated
	 *
	 * @var  Controller
	 */
	protected $controller_instance;

	/**
	 * Holds the response object of the request.
	 *
	 * @var  Response
	 */
	protected $response;

	protected $event_manager;


	/**
	 * Creates the new Request object by getting a new URI object.
	 *
	 * Usage:
	 *
	 *     $request = new Request('controller=foo&id=bar');
	 *
	 * @param   string  the uri string
	 * @param   bool    whether or not to route the URI
	 * @param   string  request method
	 * @return  void
	 */
	public function __construct(URI $uri = null, $method = 'get', $is_main = false)
	{
		$this->uri		= $uri;
		$this->method	= $method;
		$this->is_main	= $is_main;
		$this->event_manager = new EventManager;
	}

	public function setURI(URI $uri)
	{
		$this->uri = $uri;
		return $this;
	}

	public function setMethod($method)
	{
		$this->method = $method;
		return $this;
	}

	public function setResponse(Response $response)
	{
		$this->response = $response;
		return $this;
	}

	protected function exec()
	{
		if ($this->uri->getPath()) {
			throw new HttpNotFoundException();
		}

		if (!$this->uri->hasVar('controller')) {
			return null;
		}

		try {
			$class = str_replace('/', '\\', $this->uri->getVar('controller'));
			$this->controller_instance = $this->newInstance($class);
			$class = new ReflectionClass($class);

			// Method name to be invoked
			$action = $this->getAction();
			$method = $this->method.$action;
			if (!$class->hasMethod($method)) {
				$method = 'action'.$action;
				if (!$class->hasMethod($method)) {
					throw new HttpNotFoundException();
				}
			}

			$action = $class->getMethod($method);

			if (!$action->isPublic()) {
				throw new HttpNotFoundException();
			}

			$method_params = array();
			$temp_uri = clone $this->uri;
			$temp_uri->delVar('controller')->delVar('task');

			foreach($action->getParameters() as $param) {
				if (($method_param = $this->uri->getVar($param->getName())) !== null) {
					$method_params[] = $method_param;
					$temp_uri->delVar($param->getName());
				}
				/*elseif (($method_param = $this->uri->getVar($param->getPosition())) !== null) {
					$method_params[] = $method_param;
					$temp_uri->delVar($param->getPosition());
				}*/
				elseif (!$param->isDefaultValueAvailable()) {
					throw new HttpNotFoundException();
				}
			}

			// Set URI params
			$this->controller_instance->params = $temp_uri->getVars();

			if ($class->hasMethod('init')) {
				$class->getMethod('init')->invoke($this->controller_instance);
			}

			if ($this->isMain()) {
				$method = ucfirst($method);

				if ($class->hasMethod('before')) {
					$class->getMethod('before')->invoke($this->controller_instance);
				}

				if ($class->hasMethod('before'.$method)) {
					$class->getMethod('before'.$method)->invoke($this->controller_instance);
				}
			}

			$response = $action->invokeArgs($this->controller_instance, $method_params);

			if ($this->isMain()) {
				if ($class->hasMethod('after'.$method)) {
					$response = $class->getMethod('after'.$method)->invoke($this->controller_instance, $response);
				}

				if ($class->hasMethod('after')) {
					$response = $class->getMethod('after')->invoke($this->controller_instance, $response);
				}
			}
		}
		catch (HttpNotFoundException $e) {
			throw $e;
		}

		return $response;
	}

	/**
	 * This executes the request and sets the response to be used later.
	 *
	 * @return  Request  This request object
	 */
	public function execute()
	{
		$this->getEventManager()->trigger('beforeExecute', $this);
		$this->response = $this->exec();
		$this->getEventManager()->trigger('afterExecute', $this);

		return $this;
	}

	public function isMain()
	{
		return $this->is_main;
	}

	/*public function isAjax()
	{
		return false;
	}*/

	public function getURI()
	{
		return $this->uri;
	}

	public function getAction()
	{
		$action = $this->uri->getVar('task', 'Index');
		/*if (!isset($action)) {
			$class = str_replace('/', '\\', $this->uri->getVar('controller'));
			$class = new ReflectionClass($class);
			$action = $class->hasProperty('default_action') ? $class->getProperty('default_action')->getValue($this->controller_instance) : 'Index';
		}*/

		return $action;
	}

	public function getControllerInstance()
	{
		return $this->controller_instance;
	}

	public function getResponse()
	{
		return $this->response;
	}

    public function getEventManager()
	{
		return $this->event_manager;
	}

    public function get($name)
	{
		return $this->service_manager->get($name);
	}

	public function set($name, $service)
	{
		return $this->service_manager->set($name, $service);
	}

}
?>