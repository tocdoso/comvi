<?php
namespace Comvi\I18N;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\I18n\Translator\Translator;

/**
 * The TranslatorFactory class.
 *
 * @package		Comvi.Core
 */
class TranslatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$translator = new Translator;
		$translator->addTranslationFilePattern('PhpArray', PATH_APPLICATION, 'languages'.DS.'%s.php');
		$translator->setLocale($serviceLocator->get('document')->language);

		return $translator;
	}
}
