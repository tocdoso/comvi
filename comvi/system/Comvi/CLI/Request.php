<?php
namespace Comvi\CLI;

/**
 * The Request class is used to create and manage requests.
 *
 * @package		Comvi.Core
 */
class Request extends \Comvi\Routing\Request
{
	protected static function getMethod()
	{
		return 'cli';
	}

	protected function isMain()
	{
		return false;
	}
}
?>