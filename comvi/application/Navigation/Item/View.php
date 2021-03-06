<?php
namespace Navigation\Item;
use Comvi\Core\View as BaseView;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\Core\DocumentAwareInterface;
use Comvi\Core\DocumentAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;

/**
 * Navigation Item View
 */
class View extends BaseView implements URLHelperInterface, RouterAwareInterface, DocumentAwareInterface, TranslatorAwareInterface
{
	use URLHelperTrait;
	use RouterAwareTrait;
	use DocumentAwareTrait;
	use TranslatorAwareTrait;
}
?>