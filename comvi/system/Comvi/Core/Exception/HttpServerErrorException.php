<?php
namespace Comvi\Core\Exception;

/**
 * The HttpServerErrorException class.
 *
 * @package		Comvi.Core
 * @subpackage	Exception
 */
class HttpServerErrorException extends \HttpException
{
	public function getResponse()
	{
		try {
			$format		= \Loader::getInstance('document')->getType();
			$uri		= \Loader::getInstance('config', 'route')->get('500');
			$uri		= new \URI($uri);
			$uri->setVar('format', $format);
			$request	= new \Request($uri, 'get', true);
			$response	= $request->execute();
		}
		catch (\Exception $e) {
			return $this->getDefaultResponse($e->getMessage());
		}

		return $response;
	}
}
?>