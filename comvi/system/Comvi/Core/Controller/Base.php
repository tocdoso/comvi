<?php
namespace Comvi\Core;

/**
 * Controller_Base class.
 *
 * @package		Comvi.Core
 * @subpackage	Controller
 */
abstract class Controller_Base extends \Controller_Theme
{
   public function before()
   {
		parent::before();

		// Assign logged_user to the instance so controllers can use it
		$this->logged_user = Auth::getUserInfo();
		// Set a global variable so views can use it
		View::assignGlobal('logged_user', $this->logged_user);
   }
}
?>