<?php
namespace Comvi\I18N;
use Zend\I18n\Translator\Translator;

/**
 * Declare Translator Aware trait.
 *
 * @package		Comvi.I18N
 */
trait TranslatorAwareTrait
{
	protected $translator;

	public function setTranslator(Translator $translator)
	{
		$this->translator = $translator;
	}

	public function _($message, $textDomain = 'default', $locale = null)
	{
		if ($locale === null) {
			$locale = $this->document->language;
		}

		return $this->translator->translate($message, $textDomain, $locale);
	}
}
