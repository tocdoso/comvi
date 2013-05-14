<?php
namespace Comvi\Core\Helper;

/**
 * Number helper class.
 *
 * @static
 * @package		Comvi.Core
 * @subpackage	Helper
 */
class Number
{
	const DEC_POINT		= '.';
	const THOUSANDS_SEP	= ',';


	/**
	 * Convert byte to more readable format, like "1 KB" instead of "1024".
	 * cut_zero, remove the 0 after comma ex:  10,00 => 10	  14,30 => 14,3
	 *
	 * @param	int		$size
	 * @return	string
	 */
	public static function formatByte($size)
	{
		$unim = array('B','KB','MB','GB','TB','PB');

		for ($i = 0; $size >= 1024; $i++) {
			$size = $size / 1024;
		}

		return number_format($size, $i?2:0, self::DEC_POINT, self::THOUSANDS_SEP ).' '.$unim[$i];
	}
}
