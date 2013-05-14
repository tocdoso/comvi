<?php
namespace Comvi\Core\Loader;

/**
 * Load and init all the classes of the framework
 *
 * @package		Comvi.Core.Loader
 */
class ClassLoader
{
	/**
	 * @var  array  $classes	holds all classes and paths
	 */
	protected static $classes = array();

	/**
	 * @var  array  list off classes will be aliased to global class
	 */
	protected static $core_classes = array();


    /**
     * Registers this instance as an autoloader.
     *
     * @param Boolean $prepend Whether to prepend the autoloader or not
     */
    public static function register($prepend = false)
    {
        spl_autoload_register('static::loadClass', true, $prepend);
    }

    /**
     * Unregisters this instance as an autoloader.
     */
    public static function unregister()
    {
        spl_autoload_unregister('static::loadClass');
    }

	public static function loadClass($class, $isCoreClass = true)
	{
		//$class = ltrim($class, '\\');

		if (array_key_exists($class, static::$classes)) {
			$path = str_replace('/', DS, static::$classes[$class]);
			if (is_file($path)) {
				require $path;
				return true;
			}
			return false;
		}


		if (($isCoreClass === true) && isset(static::$core_classes[$class])) {
			$full_class = static::$core_classes[$class];

			if (!class_exists($full_class, false) && !interface_exists($full_class, false)) {
				$return = static::loadClass($full_class, false);
			}
			else {
				$return = true;
			}
			class_alias($full_class, $class);

			return $return;
		}


		if ($last_ns_pos = strripos($class, '\\')) {
			$namespace = substr($class, 0, $last_ns_pos);
			$class = substr($class, $last_ns_pos + 1);
		}
		else {
			$namespace = '';
		}

		$file			= str_replace('_', DS, $class) . '.php';
		$paths_packages	= FileLoader::calculatePaths($file, $namespace, 'packages');
		$paths_modules	= FileLoader::calculatePaths($file, $namespace, 'modules');
		$paths_user		= FileLoader::calculatePaths($file, $namespace, 'user');
		$paths			= array_merge($paths_packages, $paths_modules, $paths_user);

		foreach ($paths as $path) {
			if (is_file($path)) {
				require $path;
				return true;
			}
		}

		return false;
	}

	/**
	 * Add a core class, overwrite if exist.
	 *
	 * @param	string
	 * @return	void
	 */
	public static function addCoreClass($class)
	{
		$core_class = basename(str_replace('\\', DS, $class));
		static::$core_classes[$core_class] = $class;
	}

	/**
	 * Add multi core classes, overwrite if exist.
	 *
	 * @return	void
	 */
	public static function addCoreClasses()
	{
		$classes = func_get_args();

		foreach ($classes as $class) {
			static::addCoreClass($class);
		}
	}

	/**
	 * Add a class load path.  Any class added here will not be searched for
	 * but explicitly loaded from the path.
	 *
	 * @param   string  the class name
	 * @param   string  the path to the class file
	 * @return  void
	 */
	public static function addClass($class, $path)
	{
		static::$classes[$class] = $path;
	}

	/**
	 * Add multiple class paths to the load path.
	 *
	 * @param   array  the class names and paths
	 * @return  void
	 */
	public static function addClasses($classes)
	{
		foreach ($classes as $class => $path)
		{
			static::$classes[$class] = $path;
		}
	}
}
?>