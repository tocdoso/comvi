<?php
use Comvi\Core\Loader\FileLoader;
use Comvi\Core\Loader\ClassLoader;


// START Defines
	define('VERSION', '1.0.0');
	// Your environment.  Can be set to one of the following:
	// development | test | stage | production
	define('ENVIRONMENT',	isset($_SERVER['COMVI_ENV']) ? $_SERVER['COMVI_ENV'] : 'development');

	define('START_TIME',	microtime(true));
	define('START_MEM',		memory_get_usage());

	define('DS',				DIRECTORY_SEPARATOR);
	define('PATH_SYSTEM',		__DIR__.DS.'system'.DS);
	define('PATH_APPLICATION',	__DIR__.DS.'application'.DS);
	define('PATH_USER',			realpath(dirname($_SERVER['SCRIPT_FILENAME']).'/..').DS);
// END Defines

// START Inits
	error_reporting(('ENVIRONMENT' === 'production') ? 0 : E_ALL);
	unset($GLOBALS, $_ENV);

	// Loader Classes are neccessary and need to be imported first.
	require PATH_SYSTEM.'Comvi'.DS.'Core'.DS.'Loader'.DS.'FileLoader.php';
	require PATH_SYSTEM.'Comvi'.DS.'Core'.DS.'Loader'.DS.'ClassLoader.php';

	// Add default namespace for searching files.
	FileLoader::addNamespace('', PATH_SYSTEM,		'packages');
	FileLoader::addNamespace('', PATH_APPLICATION,	'modules');
	FileLoader::addNamespace('', PATH_USER,			'user');

	// Register the autoloader.
	ClassLoader::register();
// END Inits