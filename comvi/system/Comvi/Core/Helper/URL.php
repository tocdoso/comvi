<?php
namespace Comvi\Core\Helper;

/**
 * URL helper class.
 *
 * @static
 * @package		Comvi.Core
 * @subpackage	Helper
 */
class URL
{
	/**
	 * Gets the current URL, including the BASE_URL
	 *
	 * @return  string
	 */
	public static function prefix()
	{
		static $prefix;

		if (!isset($prefix)) {
			//$prefix = $_SERVER['REQUEST_SCHEME'];
			$prefix = 'http';

			// Determine if the request was over SSL (HTTPS).
			if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
				$prefix .= 's';
			}

			$prefix .= '://'.$_SERVER['HTTP_HOST'];

			if ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') {
				$prefix .= ':'.$_SERVER['SERVER_PORT'];
			}
		}
	
		return $prefix;
	}

	/**
	 * Gets the current URL, including the BASE_URL
	 *
	 * @return  string
	 */
	public static function current($pathonly = false)
	{
		static $path;

		if (!isset($path)) {
			/*
			 * Since we are assigning the URI from the server variables, we first need
			 * to determine if we are running on apache or IIS.  If REQUEST_URI
			 * are present, we will assume we are running on apache.
			 */
			if (isset($_SERVER['REQUEST_URI'])) {
				$path = $_SERVER['REQUEST_URI'];
			}
			else {
				// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
				$path = $_SERVER['SCRIPT_NAME'];
				if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
					$path .= '?'.$_SERVER['QUERY_STRING'];
				}
			}
		}

		return ($pathonly === false) ? static::prefix().$path : $path;
	}

	/**
	 * Returns the base URL for the request.
	 *
	 * @param	boolean $pathonly If false, prepend the scheme, host and port information. Default is false.
	 * @return	string	The base URL string
	 */
	public static function base($pathonly = false)
	{
		static $path;

		if (!isset($path)) {
			// guess base uri
			if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI'])) {
				// PHP-CGI on Apache with "cgi.fix_pathinfo = 0"

				// We shouldn't have user-supplied PATH_INFO in PHP_SELF in this case
				// because PHP will not work with PATH_INFO at all.
				$script_name = $_SERVER['PHP_SELF'];
			}
			else {
				//Others
				$script_name = $_SERVER['SCRIPT_NAME'];
			}

			$path = rtrim(dirname($script_name), '/\\').'/';
		}

		return ($pathonly === false) ? static::prefix().$path : $path;
	}
}
?>