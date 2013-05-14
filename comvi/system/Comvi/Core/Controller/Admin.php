<?php
namespace Comvi\Core;

/**
 * Controller_Admin class.
 *
 * @package		Comvi.Core
 * @subpackage	Controller
 */
abstract class Controller_Admin extends \Controller_Theme
{
	//public $template = 'admin/template';


	public function before()
	{
		parent::before();

		if (!\Auth::isLogged() and \Request::getURI()->getVar('controller') != 'Login') {
			\Response::redirect('?com=Auth&controller=Login&return='.\Router::_(\Request::getURI()));
		}
	}
}
?>