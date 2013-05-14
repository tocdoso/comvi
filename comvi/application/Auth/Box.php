<?php
namespace Auth;
use Comvi\Core\Exception\HttpNotFoundException;
use Comvi\Core\AbstractController;
use Comvi\Core\Response;
use Comvi\Core\SessionManagerAwareInterface;
use Comvi\Core\SessionManagerAwareTrait;
use Comvi\Core\ConfigAwareInterface;
use Comvi\Core\ConfigAwareTrait;
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
//use Comvi\Authentication\AuthenticationAwareInterface;
//use Comvi\Authentication\AuthenticationAwareTrait;
use Comvi\Authentication\Adapter;
use Zend\Session\Container; 

use PFBC\Form;
use PFBC\Element;

use StdClass;

/**
 * Auth User Box Controller
 */
class Box extends AbstractController implements SessionManagerAwareInterface, URLHelperInterface, CurrentURLAwareInterface, RouterAwareInterface, DocumentAwareInterface, TranslatorAwareInterface
{
	use SessionManagerAwareTrait;
	use URLHelperTrait;
	use CurrentURLAwareTrait;
	use RouterAwareTrait;
	use DocumentAwareTrait;
	use TranslatorAwareTrait;
	//use AuthenticationAwareTrait;

	public function init()
	{
		$this->addTranslationModule();
		$this->user = new Container('user');
	}

	public function getIndex()
	{
		$view = $this->getView($this->getParam('layout', 'box'));
		$view->assign('user', $this->user);

		return $view;
	}
}
?>