<?php
namespace Comvi\Core;
use Comvi\Core\Loader\FileLoader;

/**
 * Config Class.
 *
 * @package		Comvi.Core
 */
class Config extends \Zend\Config\Config
{
	public function setData($name = 'config', $module = '')
	{
		$paths_packages	= FileLoader::calculatePaths($name.'.php', $module, 'packages', 'config');
		$paths_modules	= FileLoader::calculatePaths($name.'.php', $module, 'modules', 'config');
		$paths_user		= FileLoader::calculatePaths($name.'.php', $module, 'user', 'config');
		$paths			= array_merge($paths_packages, $paths_modules, $paths_user);

		foreach ($paths as $key => $path) {
			if (!is_file($path)) {
				unset($paths[$key]);
			}
		}

		if (empty($paths)) {
			throw new \Exception("LIB_CONFIG_ERROR_FIND_CONFIG_FILE|{$name}|{$module}", 404);
		}

		foreach ($paths as $path) {
			$this->merge(new static(require $path));
		}
	}
}
?>