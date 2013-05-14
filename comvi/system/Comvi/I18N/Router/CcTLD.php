<?php
namespace Comvi\I18N;
use Comvi\Core\URI;

/**
 * Router_CcTLD Class.
 *
 * @package		Comvi.I18N
 */
class Router_CcTLD extends Router
{
	public function parse(URI &$uri)
	{
		$host = $uri->getHost();

		if (isset($this->supported_languages[$host])) {
			$lang = $this->supported_languages[$host];
			$uri->setVar('lang', $lang);
		}

		parent::parse($uri);
	}

	public function build(URI &$uri)
	{
		parent::build($uri);

		$langs	= array_flip($this->supported_languages);
		$lang	= $uri->getVar('lang');

		if (!empty($lang) && isset($langs[$lang])) {
			$host = $langs[$lang];
			$uri->full();
			$uri->setHost($host);
			$uri->delVar('lang');
		}
	}
}
?>