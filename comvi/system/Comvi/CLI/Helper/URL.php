<?php
namespace Comvi\CLI\Helper;

/**
 * URL helper class.
 *
 * @static
 * @package		Comvi.CLI
 * @subpackage	Helper
 */
class URL
{
	/**
	 * Gets the current URL, including the BASE_URL
	 *
	 * @return  string
	 */
	public static function current()
	{
		return implode('/', $_SERVER['argv']);
	}
}
?>