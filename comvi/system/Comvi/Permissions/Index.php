<?php
namespace Comvi\I18N;
use Exception;

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

		//$this->get('config')->class_map->request = 'Comvi\\I18N\\Request';
		//$this->get('config')->class_map->router	 = 'Comvi\\I18N\\Router';

		$this->service_manager->setFactory('Translator', 'Comvi\\I18N\\TranslatorFactory');
		$this->service_manager->setFactory('Document', 'Comvi\\I18N\\DocumentFactory');
	}
}
