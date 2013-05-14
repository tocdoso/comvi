<?php
namespace Comvi\Core\Exception;

/**
 * The HttpException class.
 *
 * @package		Comvi.Core
 * @subpackage	Exception
 */
abstract class HttpException extends \Comvi\Core\Exception
{
	/**
	 * Return default response.
	 *
	 * @return  Response
	 */
	protected function getDefaultResponse($msg = 'Some errors occurred.')
	{
		$html = '<!DOCTYPE html>'.
			'<html>'.
			'<head>'.
			'<title>Internal Server Error</title>'.
			'</head>'.
			'<body>'.
			'<h1>Internal Server Error</h1>'.
			'<p>'.$msg.'</p>'.
			'</body>'.
			'</html>';

		return new \Response($html, 500);
	}
}
?>