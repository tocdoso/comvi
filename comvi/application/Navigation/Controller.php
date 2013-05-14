<?php
namespace Navigation;
use Comvi\Core\AbstractController;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\I18N\TranslatorAwareInterface;
use Comvi\I18N\TranslatorAwareTrait;
use Comvi\ORM\EntityManagerAwareInterface;
use Comvi\ORM\EntityManagerAwareTrait;
use Comvi\Navigation\NavigationAwareInterface;
use Comvi\Navigation\NavigationAwareTrait;

/**
 * Navigation Controller.
 */
class Controller extends AbstractController implements URLHelperInterface, RouterAwareInterface, TranslatorAwareInterface, /*NavigationAwareInterface,*/ EntityManagerAwareInterface
{
	use URLHelperTrait;
	use RouterAwareTrait;
	use TranslatorAwareTrait;
	//use NavigationAwareTrait;
	use EntityManagerAwareTrait;

	public function init()
	{
		$this->item_repo = $this->em->getRepository('Entity\\Item');
		$this->module_repo = $this->em->getRepository('Entity\\Module');
		$this->privilege_repo = $this->em->getRepository('Entity\\Privilege');
		$this->addTranslationModule();
	}

	public function getIndex()
	{
		$view = $this->getView();
		$view->assign('title', $this->_('Navigation'));
		$view->assign('items', $this->item_repo->getChildren(null, true));
		$view->assign('root_modules', $this->module_repo->findBy(array('item' => null)));
		$view->assign('root_privileges', $this->privilege_repo->findBy(array('item' => null)));

		return $view;
	}

	public function afterGetIndex($response)
	{
		$this->breadcrumbs = array(
			array(
				'name'	=> $this->_('Navigation')
			)
		);

		return $response;
	}
}
?>