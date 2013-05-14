<?php
namespace Navigation;
use Comvi\Core\AbstractController;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;
use Comvi\Navigation\NavigationAwareInterface;
use Comvi\Navigation\NavigationAwareTrait;

/**
 * Navigation Breadcrumbs Controller.
 */
class Breadcrumbs extends AbstractController implements URLHelperInterface, RouterAwareInterface, TranslatorAwareInterface, NavigationAwareInterface
{
	use URLHelperTrait;
	use RouterAwareTrait;
	use TranslatorAwareTrait;
	use NavigationAwareTrait;

	public function before()
	{
		$this->addTranslationModule();
	}

	public function getIndex()
	{
		$view = $this->getView($this->getParam('layout', 'breadcrumbs'));
		$view->assign('items', $this->navigation->getBreadcrumbs((bool) $this->getParam('prepend_home_item', false)));

		return $view;
	}

	public function afterGetIndex($response)
	{
		$this->breadcrumbs = array(
			array(
				'name'	=> $this->_('Navigation'),
				'url'	=> '?controller=Navigation/Controller'
			),
			array(
				'name'	=> $this->_('Breadcrumbs')
			)
		);

		return $response;
	}
}
?>