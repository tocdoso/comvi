<?php
namespace Comvi\Core\Exception;

/**
 * The MovedPermanentlyException class.
 *
 * @package		Comvi.Core
 * @subpackage	Exception
 */
class MovedPermanentlyException extends \HttpException
{
	public function getResponse()
	{
		try {
			$document = \Loader::getInstance('document');

			$url = $this->getMessage();
			$url = new \URI($url);

			// If the headers have been sent, then we cannot send an additional location header
			// so we will response a javascript redirect statement.
			/*if (headers_sent()) {
				echo "<script>document.location.href='$url';</script>\n";
			}
			else {
				$document = \Loader::getInstance('document');

				if (mb_detect_encoding($url, 'ASCII', true)) {
					// MSIE type browser and/or server cause issues when url contains utf8 character,so use a javascript redirect method
					echo '<html><head><meta http-equiv="content-type" content="text/html; charset='.$document->charset.'" /><script>document.location.href=\''.$url.'\';</script></head><body></body></html>';
				}
				else {
					// All other browsers, use the more efficient HTTP header method
					if ($moved == true) {
						header('HTTP/1.1 301 Moved Permanently');
					}
					header('Location: '.$url);
					header('Content-Type: text/html; charset='.$document->charset);
				}
			}*/

			// Init & Send Response.
			$response = new \Response();
			$response
				->setStatus(301)
				->setHeader('Location', (string) $url)
				->setHeader('Content-Type', $document->mime.'; charset='.$document->charset);
		}
		catch (\Exception $e) {
			return $this->getDefaultResponse($e->getMessage());
		}

		return $response;
	}
}
?>