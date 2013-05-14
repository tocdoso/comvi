<?php
namespace Comvi\I18N;
use Zend\I18n\Translator\Translator;

/**
 * Declare Translator Aware interface.
 *
 * @package		Comvi.I18N
 */
interface TranslatorAwareInterface
{
	public function setTranslator(Translator $translator);
}
