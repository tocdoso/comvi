<?php
namespace Navigation;
use Comvi\Core\View as BaseView;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;

/**
 * Error View
 */
class View extends BaseView implements URLHelperInterface, RouterAwareInterface, TranslatorAwareInterface
{
	use URLHelperTrait;
	use RouterAwareTrait;
	use TranslatorAwareTrait;
}
?>