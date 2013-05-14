<?php
namespace Comvi\I18N;
use Zend\I18n\Translator\Translator;
//use Comvi\Core\Loader\FileLoader;

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

	protected function addTranslationModule($module = null, $type = 'PhpArray')
	{
		$pattern	= 'languages'.DS.'%s.php';
		$textDomain	= ($module !== null) ? $module : $this->module;
		$baseDir	= PATH_APPLICATION.str_replace('\\', DS, $textDomain);

		$this->translator->addTranslationFilePattern($type, $baseDir, $pattern, $textDomain);
	}

	protected function _($message, $textDomain = null, $locale = null)
	{
		/*if ($locale === null) {
			$locale = $this->document->language;
		}*/

		if ($textDomain === null) {
			$textDomain = $this->module;
		}

		return $this->translator->translate($message, $textDomain, $locale);
	}
}
