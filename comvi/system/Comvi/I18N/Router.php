<?php
namespace Comvi\I18N;
use Comvi\Core\RouterInterface;
use Comvi\Core\URI;
use Comvi\Core\Exception\HttpFoundException;
use Comvi\Core\Exception\HttpNotFoundException;
use Comvi\I18N\Router\Router_I18N;
use Comvi\I18N\Helper\Language;

/**
 * Class to create and parse routes.
 *
 * @package		Comvi.Routing
 */
class Router implements RouterInterface
{
	const REQUIRE_LANGUAGE		= 1;
	const NOT_REQUIRE_LANGUAGE	= 0;

	protected $use_primary_language	= true;
	protected $preferred_mode		= null;
	protected $default_language;
	protected $current_language;
	protected $supported_languages	= array();

	public function __construct($options = array())
	{
		if (array_key_exists('use_primary_language', $options)) {
			$this->use_primary_language = $options['use_primary_language'];
		}

		if (array_key_exists('preferred_mode', $options)) {
			$this->preferred_mode = $options['preferred_mode'];
		}

		if (array_key_exists('default_language', $options)) {
			$this->default_language = $options['default_language'];
		}

		if (array_key_exists('supported_languages', $options)) {
			$this->supported_languages = $options['supported_languages'];
		}
	}

	public function setCurrentLanguage($language)
	{
		$this->current_language = $language;
	}

	public function getCurrentLanguage()
	{
		return $this->current_language ? $this->current_language : $this->default_language;
	}

	public function checkAfterParse(URI $uri)
	{
		$this->checkPreferredMode($uri);
		$this->checkLanguageIsSupported($uri);
	}

	protected function checkPreferredMode(URI $uri)
	{
		if (!$uri->hasVar('lang') && $this->preferred_mode === static::REQUIRE_LANGUAGE) {
			$uri->setVar('lang', $this->detectUserLanguage());
			$this->build($uri);
			throw new HttpFoundException($uri);
		}

		if ($uri->getVar('lang') === $this->default_language && $this->preferred_mode === static::NOT_REQUIRE_LANGUAGE) {
			$uri->delVar('lang');
			//$this->build($uri);
			throw new HttpFoundException($uri);
		}
	}

	protected function checkLanguageIsSupported(URI $uri)
	{
		// If language is not supported, throw NotFound Exception.
		$lang = $uri->getVar('lang');
		if ($lang && !in_array($lang, $this->supported_languages)) {
			throw new HttpNotFoundException();
		}
	}

	/**
	 * Parse the URI.
	 *
	 * @param	object	The URI
	 *
	 * @return	void
	 */
	public function parse(URI &$uri)
	{
	}

	/**
	 * Build the URI.
	 *
	 * @param	object	The URI
	 *
	 * @return	void
	 */
	public function build(URI &$uri)
	{
		if (!$uri->hasVar('lang') && $this->current_language) {
			$uri->setVar('lang', $this->current_language);
		}

		if ($uri->getVar('lang') === $this->default_language && $this->preferred_mode === static::NOT_REQUIRE_LANGUAGE) {
			$uri->delVar('lang');
		}
	}

	protected function detectUserLanguage()
	{
		$lang = Language::detectUserLanguage($this->use_primary_language);

		if (!in_array($lang, $this->supported_languages)) {
			$lang = $this->default_language;
		}

		return $lang;
	}
}
