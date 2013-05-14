<?php
namespace Comvi\Core\Loader;

/**
 * FileLoader Class.
 *
 * @package		Comvi.Core.Loader
 */
class FileLoader
{
 	/**
	 * @var  array  $namespaces	holds all namespaces and paths
	 */
	protected static $namespaces = array();


	public static function getFilesLoaded()
	{
		return get_included_files();
	}

	public static function countFilesLoaded()
	{
		return count(get_included_files());
	}

	/*public static function load($file, $namespace = '', $type = '', $needle = '')
	{
		$paths = static::calculatePaths($file, $namespace, $type, $needle);

		foreach ($paths as $path) {
			if (is_file($path)) {
				require $path;
				return true;
			}
		}

		return false;
	}*/

	public static function calculatePaths($file, $namespace = '', $type = '', $needle = '')
	{//print_r(static::$namespaces);die();
		if (!isset(static::$namespaces[$type]) || empty(static::$namespaces[$type])) {
			throw new \Exception("LIB_LOADER_ERROR_GET_PATHS_NAMESPACE_PATH_NOT_FOUND|$type", 404);
		}

		// Guess paths that file maybe located at.
		$file_paths	= static::calculateShortPaths($file, $namespace, $needle);
		$paths		= array();

		foreach (static::$namespaces[$type] as $ns => $ns_paths) {
			//$ns = ltrim($ns, '\\');
			foreach ($ns_paths as $ns_path) {
				foreach ($file_paths as $file_path) {
					if (stripos($file_path, $ns) === 0 || $ns === '') {
						$path = $ns_path.str_replace('\\', DS, substr($file_path, strlen($ns)));
						$paths[] = $path;
					}
				}
			}
		}

		return $paths;
	}

	/**
	 * Example
	 * Loader::calculateShortPaths('X.php', 'A\\B\\C', 'type');
	 *
	 * Result
	 * Array
	 * (
	 *     [0] => type/A/B/C/X.php
	 *     [1] => A\type/B/C/X.php
	 *     [2] => A\B\type/C/X.php
	 *     [3] => A\B\C\type/X.php
	 * )
	 */
	protected static function calculateShortPaths($filename, $namespace = '', $needle = '')
	{
		// Normalize namespace.
		static::normalizeNamspace($namespace);

		if (empty($needle)) {
			return array($namespace.$filename);
		}

		/*if (empty($namespace)) {
			return array($needle.DS.$filename);
		}*/

		$namespace = explode('\\', $namespace);
		$paths = array();

		for ($pos = 0; $pos < count($namespace); $pos++) {
			$part1 = array_slice($namespace, 0, $pos);
			$part2 = array_slice($namespace, $pos);
			$paths[] = ltrim(implode('\\', $part1).'\\'.$needle.DS.implode(DS, $part2).$filename, '\\');
		}

		return $paths;
	}

	/**
	 * Normalize namespace.
	 */
	protected static function normalizeNamspace(&$namespace)
	{
		$namespace = trim($namespace, '\\');

		if (!empty($namespace)) {
			$namespace .= '\\';
		}
	}

	/**
	 * Add a namespace load path.
	 *
	 * @param   string  the namespace name
	 * @param   string  the path to the namespace directory
	 * @param   string	namespace type: class|config|template|language
	 * @param   bool	prepend or append namespace
	 * @return  void
	 */
	public static function addNamespace($namespace, $path, $type = '', $prepend = true)
	{
		// Normalize namespace.
		static::normalizeNamspace($namespace);

		if (!array_key_exists($type, static::$namespaces)) {
			static::$namespaces[$type] = array();
		}

		if (!array_key_exists($namespace, static::$namespaces[$type])) {
			static::$namespaces[$type][$namespace] = array($path);
			krsort(static::$namespaces[$type]);
		}
		elseif ($prepend) {
			array_unshift(static::$namespaces[$type][$namespace], $path);
		}
		else {
			array_push(static::$namespaces[$type][$namespace], $path);
		}
	}
}
?>