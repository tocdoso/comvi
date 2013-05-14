<?php
namespace Comvi\Authentication;
use Comvi\Core\Exception\HttpFoundException;
use Comvi\Core\Helper\URL;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use LightOpenID;

/**
 * Adapter class.
 *
 * @package		Comvi.Authentication
 */
class Adapter implements AdapterInterface
{
    /**
     * Sets $identity for authentication
     *
     * @return void
     */
    public function __construct($identity, $required = array())
    {
		$this->openid = new LightOpenID(URL::prefix());
		$this->openid->identity = $identity;
		if (!empty($required)) {
			$this->openid->required = $required;
		}
		else {
			$this->openid->required = array(
				'namePerson/first',
				'namePerson/last',
				'contact/email'
			);
		}
    }

	public function openid()
	{
		return $this->openid;
	}

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
		if (!$this->openid->mode) {
			throw new HttpFoundException($this->openid->authUrl());
		}
		elseif ($this->openid->mode == 'cancel') {
			return new Result(Result::FAILURE, $this->openid->identity, array('User has canceled authentication!'));
		}
		elseif ($this->openid->validate()) {
			return new Result(Result::SUCCESS, $this->openid->identity);
		}
		else {
			return new Result(Result::FAILURE, $this->openid->identity);
		}
    }
}
?>