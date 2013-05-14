<?php
namespace Comvi\I18N;
use Comvi\Core\URI;

/**
 * Router_Subdirectory Class.
 *
 * @package		Comvi.I18N
 */
class Router_Subdirectory
{
	public function parse(URI &$uri)
	{
		// Clean URI.
		$uri->shorten();

		$path = $uri->getPath();
		$pos = strpos($path, '/');
		// Redirect if no language was defined in URL.
		if ($pos === false || $pos > 7) {
			return;
		}

		$lang = substr($path, 0, $pos);
		$path = substr($path, $pos+1);

		// Set $uri for later routing.
		$uri->setVar('lang', $lang);
		$uri->setPath($path);

		// Restore uri for later routing.
		//$uri->full();

		parent::parse($uri);
	}

	public function build(URI &$uri)
	{
		if ($uri->hasVar('lang')) {
			$uri->prepend($uri->getVar('lang').'/');
			$uri->delVar('lang');
		}
	}
}
?>