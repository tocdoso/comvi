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
use Comvi\ORM\EntityManagerAwareInterface;
use Comvi\ORM\EntityManagerAwareTrait;
//use Comvi\Authentication\AuthenticationAwareInterface;
//use Comvi\Authentication\AuthenticationAwareTrait;
use Comvi\Authentication\Adapter;
use Zend\Session\Container; 

use PFBC\Form;
use PFBC\Element;

use StdClass;

/**
 * Auth Controller
 */
class Controller extends AbstractController implements SessionManagerAwareInterface, ConfigAwareInterface, URLHelperInterface, CurrentURLAwareInterface, RouterAwareInterface, DocumentAwareInterface, TranslatorAwareInterface, EntityManagerAwareInterface/*, AuthenticationAwareInterface*/
{
	use SessionManagerAwareTrait;
	use ConfigAwareTrait;
	use URLHelperTrait;
	use CurrentURLAwareTrait;
	use RouterAwareTrait;
	use DocumentAwareTrait;
	use TranslatorAwareTrait;
	use EntityManagerAwareTrait;
	//use AuthenticationAwareTrait;

	static protected function getGavatar($email, $size = '')
	{
		$gravatar_md5	= !empty($email) ? md5(strtolower($email)) : '';
		$size			= !empty($size) ? '?s='.$size : '';
		return 'http://www.gravatar.com/avatar/'.$gravatar_md5.$size;
	}

	public function init()
	{
		$this->addTranslationModule();
		$this->user = new Container('user');
	}

	public function getIndex()
	{
		$view = $this->getView();
		$view->assign('user', $this->user);
		/*$view->assign('return_url', $this->getReturnURL());*/
		return $view;
	}

	public function getLogin($identifier = null)
	{
		if($this->user->email) {
			return $this->getReturnResponse();
		}

		if ($identifier === null) {
			$form = $this->getForm();
			$view = $this->getView();
			$view->assign('title', 'Login');
			$view->assign('form', $form->render(true), false);

			return $view;
		}

		if (!$identity = $this->config->openid->$identifier) {
			throw new HttpNotFoundException();
		}

		$authAdapter = new Adapter($identity);
		$result = $authAdapter->authenticate();

		if ($result->isValid()) {
			$attrs	= $authAdapter->openid()->getAttributes();
			// Store user in session
			$this->user->email	= $attrs['contact/email'];
			$this->user->fullname	= $attrs['namePerson/first'].' '.$attrs['namePerson/last'];
			$this->user->avatar	= static::getGavatar($this->user->email, 32);

			$user = $this->getUserByEmail($this->user->email);
			if ($user !== null) {
				$this->user->id = $user->getId();
				$this->user->roles = array();
				foreach ($user->getRoles() as $role) {
					$this->user->roles[] = $role->getName();
				}
			}

			return $this->getReturnResponse();
		}

		print_r($_SESSION);
		die('invalid');
	}

	public function getLogout()
	{
		if($this->user->email) {
			$this->user->getManager()->getStorage()->clear('user');
		}
	
		return $this->getReturnResponse();
	}

	protected function getReturnResponse()
	{
		$response = new Response;
		$response
			->setStatus(302)
			->setHeader('Location', $this->getReturnURL());

		return $response;
	}

	protected function getReturnURL()
	{
		return isset($this->params['return']) ? $this->params['return'] : $this->build($this->parse($this->url('')));
	}

	protected function getForm()
	{
		$this->document->addStyle('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css');
		$static_url = $this->document->static_url;
		$form = new Form("login");
		$form->configure(array(
			"method" => 'get',
			"action" => $this->build($this->current_url),
			"prevent" => array('bootstrap', 'jQuery'),
			"resourcesPath" => substr($static_url, 0, -1)
		));
		$form->addElement(new Element\Radio("Identifier", "identifier", array('google' => 'Google'), array('value' => 'google')));
		$form->addElement(new Element\Button);
		$form->addElement(new Element\Button("Cancel", "button", array(
			"onclick" => "history.go(-1);"
		)));

		return $form;
	}

	protected function getUserByEmail($email)
	{
		return $this->em->getRepository('Entity\\User')->findOneBy(array('email' => $email) /*user email*/);
	}
}
?>