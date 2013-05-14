<?php
namespace My;
use Comvi\Core\View as BaseView;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\ProfilerAwareInterface;
use Comvi\Core\ProfilerAwareTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\Core\DocumentAwareInterface;
use Comvi\Core\DocumentAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;

/**
 * My View
 */
class View extends BaseView implements URLHelperInterface, ProfilerAwareInterface, RouterAwareInterface, DocumentAwareInterface, TranslatorAwareInterface
{
	use URLHelperTrait;
	use ProfilerAwareTrait;
	use RouterAwareTrait;
	use DocumentAwareTrait;
	use TranslatorAwareTrait;
}
?>