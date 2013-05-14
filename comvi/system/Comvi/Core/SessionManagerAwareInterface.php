<?php
namespace Comvi\Core;
use Zend\Session\SessionManager;

/**
 * Declare SessionManager Aware interface.
 *
 * @package		Comvi.Core
 */
interface SessionManagerAwareInterface
{
	public function setSessionManager(SessionManager $document);
}
