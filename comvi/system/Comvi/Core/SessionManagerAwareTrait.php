<?php
namespace Comvi\Core;
use Zend\Session\SessionManager;

/**
 * Declare SessionManager Aware trait.
 *
 * @package		Comvi.Core
 */
trait SessionManagerAwareTrait
{
	protected $session_manager;

	public function setSessionManager(SessionManager $session_manager)
	{
		$this->session_manager = $session_manager;
	}
}
