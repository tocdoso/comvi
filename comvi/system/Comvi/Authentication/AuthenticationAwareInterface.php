<?php
namespace Comvi\Authentication;
use Zend\Authentication\AuthenticationService;

/**
 * Declare Authentication Aware interface.
 *
 * @package		Comvi.Authentication
 */
interface AuthenticationAwareInterface
{
	public function setAuthentication(AuthenticationService $auth);
}
