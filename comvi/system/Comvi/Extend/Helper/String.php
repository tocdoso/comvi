<?php
namespace Comvi\Helper;

/**
 * String handling class for utf-8 data
 * Wraps the phputf8 library
 * All functions assume the validity of utf-8 strings.
 *
 * @static
 * @package		Comvi.Framework
 * @subpackage	Helper
 */
abstract class String
{
	/**
	 * Provides a secure hash based on a seed
	 *
	 * @param	string	$seed	Seed string.
	 * @return	string
	 */
	/*public static function getHash($seed, $salt = null)
	{
		if ($salt == null) {
			$salt = \Loader::getConfig()->get('secret');
		}

		return md5($seed.$salt);
	}*/

	public static function generateRandomString($length = 5) {
		$chars			= '0123456789abcdefghijklmnopqrstuvwxyz';
		$chars_length	= strlen($chars)-1;
		$result			= '';

		for ($i = 0; $i < $length; $i++) {
			$result .= $chars[mt_rand(0, $chars_length)];
		}

		return $result;
	}

	public static function generateSalt() {
		return self::generateRandomString(5);
	}
}
