<?php
namespace Comvi\I18N;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The DocumentFactory class.
 *
 * @package		Comvi.I18N
 */
class DocumentFactory extends \Comvi\Core\DocumentFactory
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$document = parent::createService($serviceLocator);

		// If language is not supported, use default language.
		$document->language = $serviceLocator->get('router')->getRouter('i18n')->getCurrentLanguage();

		return $document;
	}
}
