<?php
namespace Comvi\Routing;
use ReflectionClass;
use Comvi\Core\RouterInterface;
use Comvi\Core\Exception\HttpNotFoundException;
use Comvi\Core\URI;

/**
 * Class to create and parse routes.
 *
 * @package		Comvi.Routing
 */
class Router implements RouterInterface
{
	const ROUTING_MODE_RAW	= 0;
	const ROUTING_MODE_SEF	= 1;

	/**
	 * The rewrite mode
	 *
	 * @var integer
	 */
	protected $mode = self::ROUTING_MODE_RAW;

	protected $index_filename = '';

	protected $suffix = '';

	protected $rules = array();

	protected $reverse_rules = array();


	public function __construct($options = array())
	{
		if (array_key_exists('mode', $options)) {
			$this->mode = $options['mode'];
		}

		if (array_key_exists('index_filename', $options)) {
			$this->index_filename = $options['index_filename'];
		}

		if (array_key_exists('suffix', $options)) {
			$this->suffix = $options['suffix'];
		}

		if (array_key_exists('rules', $options)) {
			$this->rules = $options['rules'];
		}

		if (array_key_exists('reverse_rules', $options)) {
			$this->reverse_rules = $options['reverse_rules'];
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
		if ($this->mode === static::ROUTING_MODE_RAW) {
			return;
		}

		/*if (!$uri->isInternal()) {
			throw new \Exception('LIB_ROUTER_ERROR_CAN_NOT_PARSE_EXTERNAL_URI');
		}*/

		$uri->shorten();
		//$this->removeIndexFile($uri);
		//$uri->trim();
		$this->parseSuffix($uri);
		$this->removeIndexFilename($uri);
		$this->parseRules($uri);
		$this->parseController($uri);
		$this->parseMethodParams($uri);
		//print_r($uri);die('aaa');
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
		if ($this->mode === static::ROUTING_MODE_RAW) {
			return;
		}

		$this->buildMethodParams($uri);
		$this->buildController($uri);
		$this->buildRules($uri);

		//$uri->trim();
		$this->addIndexFilename($uri);
		$this->buildSuffix($uri);
	}

	protected function parseSuffix(URI &$uri)
	{
		$path = $uri->getPath();

		if ($suffix = pathinfo($path, PATHINFO_EXTENSION)) {
			if ($suffix !== $this->suffix) {
				$uri->setVar('format', $suffix);
			}

			$path = substr($path, 0, -strlen('.'.$suffix));
			$uri->setPath($path);
		}
	}

	protected function removeIndexFilename(URI &$uri)
	{
		if ($uri->getPath() === $this->index_filename) {
			$uri->setPath('');
		}
	}

	protected function parseRules(URI &$uri)
	{
		$path = $uri->getPath();
		foreach ($this->rules as $rule) {
			extract($rule);
			if (preg_match('#'.$pattern.'#', $path)) {
				$target = preg_replace('#'.$pattern.'#', $target, $path);
				//$target = new URI($target);
				//$uri->setPath($target->getPath());
				//$uri->setVars($target->getVars());
				$uri->setPath($target);
				break;
			}
		}
	}

	protected function parseController(URI &$uri)
	{
		$path = $uri->getPath();

		if (!empty($path)) {
			$segments		= explode('/', $path);
			$temp_segments	= $segments;

			while (!empty($temp_segments)) {
				// Guess controller name.
				$controller = implode('\\', $temp_segments);

				if (class_exists($controller)) {
					$uri->setVar('controller', str_replace('\\', '/', $controller));
					$method_params = array_slice($segments, count($temp_segments));
					$uri->setVars($method_params);
					// Re-set the URI
					$uri->setPath(null);
					break;
				}

				array_pop($temp_segments);
			}
		}
	}

	protected function parseMethodParams(URI &$uri)
	{
		if (!$uri->getVar('controller')) {
			return;
		}

		$actions = array('post', 'get', 'action');
		$task	 = $uri->getVar('task', 'Index');

		$class = str_replace('/', '\\', $uri->getVar('controller'));
		$class = new ReflectionClass($class);

		foreach ($actions as $action) {
			$method = $action.$task;

			if ($class->hasMethod($method)) {
				$method = $class->getMethod($method);
				//if ($method->getNumberOfRequiredParameters() <= count($method_params) && $method->getNumberOfParameters() >= count($method_params)) {
					foreach($method->getParameters() as $param) {
						if ($uri->hasVar($param->getPosition())) {
							$uri->setVar($param->getName(), $uri->getVar($param->getPosition()));
							$uri->delVar($param->getPosition());
						}
					}

					//
					foreach ($uri->getVars() as $key => $val) {
						if (is_int($key)) {
							$this->undoParse($uri);
							break;
						}
					}

					return;
				//}
			}
		}

		//undo parse
		$this->undoParse($uri);
		//throw new HttpNotFoundException();
	}

	protected function undoParse(URI &$uri)
	{
		$uri->setPath($uri->getVar('controller'));
		$uri->delVar('controller');
		foreach ($uri->getVars() as $key => $val) {
			if (is_int($key)) {
				$uri->append('/'.$val);
				$uri->delVar($key);
			}
		}
	}

	protected function buildRules(URI &$uri)
	{
		$path = $uri->getPath();
		foreach ($this->reverse_rules as $rule) {
			extract($rule);
			if (preg_match('#'.$pattern.'#', $path)) {
				$path = preg_replace('#'.$pattern.'#', $target, $path);
				$uri->setPath($path);
				break;
			}
		}
	}

	protected function addIndexFilename(URI &$uri)
	{
		if (!$uri->getPath()) {
			$uri->setPath($this->index_filename);
		}
	}

	protected function buildSuffix(URI &$uri)
	{
		if (!$uri->getPath()) {
			return;
		}

		if ($uri->hasVar('format')) {
			$suffix = $uri->getVar('format');
			$uri->delVar('format');
		}
		else {
			$suffix = $this->suffix;
		}

		if (!empty($suffix)) {
			$uri->append('.'.$suffix);
		}
	}

	protected function buildController(URI &$uri)
	{
		if ($uri->hasVar('controller')) {
			$uri->prepend($uri->getVar('controller'));
			$uri->delVar('controller');
		}
	}

	protected function buildMethodParams(URI &$uri)
	{
		if (!$uri->getVar('controller')) {
			return;
		}
		$actions = array('post', 'get', 'action');

		$controller	= $uri->getVar('controller');
		$task		= $uri->getVar('task', 'Index');
		$class		= str_replace('/', '\\', $controller);
		$class		= new ReflectionClass($class);

		foreach ($actions as $action) {
			$method = $action.$task;

			if ($class->hasMethod($method)) {
				$method = $class->getMethod($method);

				foreach($method->getParameters() as $param) {
					if ($uri->hasVar($param->getName())) {
						$uri->append('/'.$uri->getVar($param->getName()));
						$uri->delVar($param->getName());
					}
				}

				return;
			}
		}
	}
}
?>