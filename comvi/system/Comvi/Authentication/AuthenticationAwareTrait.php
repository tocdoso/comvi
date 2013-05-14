<?php
namespace Comvi\Authentication;
use Zend\Authentication\AuthenticationService;

/**
 * Declare Authentication Aware trait.
 *
 * @package		Comvi.Authentication
 */
trait AuthenticationAwareTrait
{
	protected $auth;

	public function setAuthentication(AuthenticationService $auth)
	{
		$this->auth = $auth;
	}
}
