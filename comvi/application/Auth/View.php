<?php
namespace Auth;
use Comvi\Core\View as BaseView;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\CurrentURLAwareInterface;
use Comvi\Core\CurrentURLAwareTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\Core\DocumentAwareInterface;
use Comvi\Core\DocumentAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;

/**
 * Error View
 */
class View extends BaseView implements URLHelperInterface, CurrentURLAwareInterface, RouterAwareInterface, DocumentAwareInterface, TranslatorAwareInterface
{
	use URLHelperTrait;
	use CurrentURLAwareTrait;
	use RouterAwareTrait;
	use DocumentAwareTrait;
	use TranslatorAwareTrait;
}
?>