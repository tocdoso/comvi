<?php
namespace Language;
use Locale;
use Comvi\Core\AbstractController;
use Comvi\Core\ConfigAwareInterface;
use Comvi\Core\ConfigAwareTrait;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\Core\DocumentAwareInterface;
use Comvi\Core\DocumentAwareTrait;

/**
 * Languages Controller.
 */
class Languages extends AbstractController implements ConfigAwareInterface, URLHelperInterface, RouterAwareInterface, DocumentAwareInterface
{
	use ConfigAwareTrait;
	use URLHelperTrait;
	use RouterAwareTrait;
	use DocumentAwareTrait;

	public function getIndex()
	{
		$supported_languages = $this->config->i18n->supported_languages->toArray();
		$links = $this->document->getHeadElements('link');
		$languages = array_flip((array) $supported_languages);
		$languages = array_map(function($v){ return null; }, $languages);

		foreach ($links as $link) {
			if ($link->get('rel') === 'alternate'
				&& ($lang_code = $link->get('hreflang'))
				&& ($href = $link->get('href'))) {
				$languages[$lang_code] = array(
					'name'	=> Locale::getDisplayName($lang_code, $lang_code),
					'href'	=> $href
				);
			}
		}

		foreach ($languages as $lang_code => $language) {
			if ($language === null) {
				$languages[$lang_code] = array(
					'name'	=> Locale::getDisplayName($lang_code, $lang_code),
					'href'	=> $this->build($this->url('?lang='.$lang_code))
				);
			}
		}

		if (isset($this->document->language) && isset($languages[$this->document->language])) {
			$languages[$this->document->language]['active'] = true;
		}

		$view = $this->getView($this->getParam('layout', 'list'));
		$view->assign('items', $languages);

		return $view;
	}
}
?>