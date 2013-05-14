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
 * Navigation Menu Controller.
 */
class Menu extends AbstractController implements URLHelperInterface, RouterAwareInterface, TranslatorAwareInterface, NavigationAwareInterface
{
	use URLHelperTrait;
	use RouterAwareTrait;
	use TranslatorAwareTrait;
	use NavigationAwareTrait;

	public function before()
	{
		$this->addTranslationModule();
	}

	public function getIndex($root = null)
	{
		$include_root_level = (bool) $this->getParam('include_root_level', 0);
		$items = $include_root_level ? array($this->navigation->getItem($root)) : $this->navigation->getChildren($root);
		$view = $this->getView($this->getParam('layout', 'menu'));
		$view->assign('items', $items);

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
				'name'	=> $this->_('Menu')
			)
		);

		return $response;
	}
}
?>