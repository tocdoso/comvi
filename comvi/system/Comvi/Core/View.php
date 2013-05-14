<?php
namespace Comvi\Core;
use Comvi\Core\Loader\FileLoader;
use Comvi\Core\View\Placeholder;

/**
 * Base class for a Comvi View.
 *
 * Class holding methods for displaying presentation data.
 *
 * @package		Comvi.Core
 */
class View
{
	/**
	 * @var  array  Holds a list of filter rules.
	 */
	protected static $filter_rules = array('htmlspecialchars');


	protected $placeholders = array();
	protected $data = array();

	/**
	 * @var  array  Holds a list local variable names need to be filtered.
	 */
	protected $filter = array();

	/**
	* Path to template folder
	*
	* @var string
	*/
	protected $module = '';

	/**
	 * Layout name
	 *
	 * @var		string
	 */
	protected $layout;

	/**
	 * Layout ext
	 *
	 * @var		string
	 */
	protected $layout_ext = '.php';


	/**
	 * Constructor
	 */
	public function __construct($options = array())
	{
		if (array_key_exists('module', $options))  {
			$this->module = $options['module'];
		}

		if (array_key_exists('layout', $options))  {
			$this->layout = $options['layout'];
		}

		if (array_key_exists('layout_ext', $options))  {
			$this->layout_ext = $options['layout_ext'];
		}
	}

	public function placeholder($name)
	{
		if (!isset($this->placeholders[$name])) {
			$this->placeholders[$name] = new Placeholder;
		}

		return $this->placeholders[$name];
	}

	/**
	 * Assigns a variable by name. Assigned values will be available as a
	 * variable within the view file:
	 *
	 *     // This value can be accessed as $foo within the view
	 *     $view->set('foo', 'my value');
	 *
	 * @param   string   variable name
	 * @param   mixed    value
	 * @param   bool     whether to filter the data or not
	 * @return  bool
	 */
	public function assign($key, $val, $filter = true)
	{
		if (is_string($key)) {
			$this->data[$key] = $val;

			if ($filter === true) {
				$this->filter[] = $key;
			}

			return true;
		}

		return false;
	}

	/**
	 * Assigns a value by reference. The benefit of binding is that values can
	 * be altered without re-setting them. It is also possible to bind variables
	 * before they have values. Assigned values will be available as a
	 * variable within the view file:
	 *
	 *     // This reference can be accessed as $ref within the view
	 *     $view->bind('ref', $bar);
	 *
	 * @param   string   variable name
	 * @param   mixed    referenced variable
	 * @param   bool     Whether to filter the var on output
	 * @return  bool
	 */
	function bind($key, &$val, $filter = true)
	{
		if (is_string($key)) {
			$this->data[$key] = &$val;

			if ($filter === true) {
				$this->filter[] = $key;
			}

			return true;
		}

		return false;
	}

	/**
	 * Retrieves all the data. It filters the data if necessary.
	 *
	 *     $data = $this->getData();
	 *
	 * @return  array   view data
	 */
	protected function getData()
	{
		// clean function.
		$clean_it = function($data, $filter, $filter_rules) {
			// apply filter rules function.
			$apply_filter_rules = function(&$var, $filter_rules) {
				if (is_string($var)) {
					foreach ($filter_rules as $rule) {
						$var = $rule($var); 
					}
				}
			};

			// apply filter rules.
			foreach ($data as $key => &$val) {
				if (in_array($key, $filter)) {
					$apply_filter_rules($val, $filter_rules);
				}
			}

			return $data;
		};

		$data = array();

		if (!empty($this->data)) {
			$data += $clean_it($this->data, $this->filter, static::$filter_rules);
		}

		return $data;
	}

	/**
	 * Return first template file matchs layout.
	 */
	protected function getPath($layout = null)
	{
		if ($layout == null) {
			$layout = $this->layout;
		}

		$file			= $layout.$this->layout_ext;
		$paths_modules	= FileLoader::calculatePaths($file, $this->module, 'modules', 'templates');
		$paths_themes	= FileLoader::calculatePaths($file, $this->module, 'themes');
		$paths			= array_merge($paths_themes, $paths_modules);

		foreach ($paths as $path) {
			if (file_exists($path)) {
				return $path;
			}
		}

		return null;
	}

	/**
	* Execute and display a template script.
	*
	* @param string The name of the template file to parse;
	* automatically searches through the template paths.
	*/
	public function display($layout = null)
	{
		$path = $this->getPath($layout);

		if ($path === null) {
			echo '<'.$layout.' not found>';
			return;
		}

		//extract($this->data, EXTR_REFS);

		$data = $this->getData();
		extract($data, EXTR_REFS);

		// include the requested template filename in the local scope
		// (this will execute the view logic).
		include $path;
	}

	/**
	 * Renders the view object to a string. Global and local data are merged
	 * and extracted to create local variables within the view file.
	 *
	 *     $output = $view->render();
	 *
	 * [!!] Global variables with the same key name as local variables will be
	 * overwritten by the local variable.
	 *
	 * @param    string  view filename
	 * @return   string
	 */
	public function render($layout = null)
	{
		try {
			$content = null;			// Handle output
			ob_start();
			$this->display($layout);
			$content = ob_get_clean();
		}
		catch (\Exception $e) {
			return $e->getMessage();
		}

		return $content;
	}

	public function __set($name, $value)
	{
		$this->assign($name, $value);
	}

	public function __get($name)
	{
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}

	/**
	 * Magic method, returns the output of [static::render].
	 *
	 * @return  string
	 * @uses    View::render
	 */
	public function __toString()
	{
		return $this->render();
	}
}
