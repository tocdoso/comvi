<?php
namespace Comvi\I18N;
use Exception;
use Comvi\Core\URI;
//use Comvi\Core\Loader\FileLoader;

/**
 * Comvi/I18N bootstrap class.
 */
class Index extends \Comvi\Core\Package
{
	public function index()
	{
		if (!extension_loaded('intl')) {
			throw new Exception('PKG_I18N_ERROR_CAN_NOT_LOAD');
		}

		//FileLoader::addNamespace('', PATH_APPLICATION.'languages'.DS, 'languages');
		//FileLoader::addNamespace('', PATH_USER.'languages'.DS, 'languages');

		$this->service_manager->setFactory('Translator', 'Comvi\\I18N\\TranslatorFactory');
		$this->service_manager->setFactory('Document', 'Comvi\\I18N\\DocumentFactory');

		$driver	 = $this->get('config')->i18n->driver;
		$class	 = 'Comvi\\I18N\\Router_'.ucfirst($driver);
		$options = $this->get('config')->i18n->toArray();
		$router	 = new $class($options);
		$this->get('router')->addRouter($router, 'i18n');


		$this->get('event_manager')->attach('afterRoute', function ($e) {
			$current_url = $e->getTarget()->getCurrentURL();
			$e->getTarget()->getRouter()->getRouter('i18n')->setCurrentLanguage($current_url->getVar('lang'));
			$current_url->delVar('lang');
		});

		$this->get('request')->getEventManager()->attach('afterExecute', function ($e) {
			$request	= $e->getTarget();
			$instance	= $request->getControllerInstance();

			// function addRefLang
			$addRefLang = function ($lang_code, $href = null) use ($request) {
				if ($href === null) {
					$href = clone $request->getURI();
					//$href = clone $request->get('current_url');
					//print_r($request->getResponse());die();
					//$href = ($request->getResponse()->getStatus() === 200) ? clone $request->getURI() : new URI;
					$href->setVar('lang', $lang_code);
					$request->get('router')->build($href);
				}
				$link = '<'.$href.'>; rel="alternate"; hreflang="'.$lang_code.'"';

				$request->getResponse()->addHeader('Link', $link);

				$request->get('document')->addLink(array(
					'rel'		=> 'alternate',
					'hreflang'	=> $lang_code,
					'href'		=> $href
				));
			};

			if (isset($instance->reflangs)) {
				foreach ($instance->reflangs as $lang_code => $href) {
					if ($lang_code === $request->get('document')->language) {
						continue;
					}

					$addRefLang($lang_code, $href);
				}
			}
			else {
				$supported_languages = $request->get('config')->i18n->supported_languages->toArray();
				$supported_languages = array_flip($supported_languages);

				foreach ($supported_languages as $lang_code => $val) {
					if ($lang_code === $request->get('document')->language) {
						continue;
					}

					$addRefLang($lang_code);
				}
			}
		});
	}
}
