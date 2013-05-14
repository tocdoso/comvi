<?php
namespace Comvi\I18N\Helper;
use Locale;

/**
 * Language helper class.
 *
 * @static
 * @package		Comvi.I18N
 * @subpackage	Helper
 */
class Language
{
	/**
	 * Detect user language.
	 *
	 * @return  string
	 */
	public static function detectUserLanguage($use_primary_language = true)
	{
		$language = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

		if ($use_primary_language === true) {
			$language = Locale::getPrimaryLanguage($language);
		}
		else {
			$language = str_replace('_', '-', $language);
		}
	
		return $language;
	}
}
?>