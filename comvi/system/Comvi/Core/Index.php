<?php
namespace Comvi\Core;
use Comvi\Core\Loader\FileLoader;
use Comvi\Core\Exception\HttpNotFoundException;

/**
 * Comvi/Core bootstrap class.
 */
class Index extends Package
{
	public function index()
	{
		$template = $this->get('config')->template;
		FileLoader::addNamespace('', PATH_APPLICATION.'templates'.DS.$template.DS, 'themes');
		FileLoader::addNamespace('', PATH_USER.'templates'.DS.$template.DS, 'themes');

		$this->get('request')->getEventManager()->attach('beforeExecute', function ($e) {
			$request = $e->getTarget();
			if ($request->getURI()->getVar('format', 'html') !== $request->get('document')->type) {
				throw new HttpNotFoundException();
			}
		});

		$this->get('request')->getEventManager()->attach('afterExecute', function ($e) {
			$response = $e->getTarget()->getResponse();

			// Make sure the $response is a Response object
			if (!$response instanceof Response) {
				$e->getTarget()->setResponse(new Response($response));
			}
		});

		$this->get('request')->getEventManager()->attach('afterExecute', function ($e) {
			$request = $e->getTarget();
			//if ($request->isMain()) {
				$format		= $request->get('document')->type;
				$headers	= $request->getResponse()->getHeaders();

				// Init site theme (Response body).
				$options = array(
					'layout'	=> 'theme',
					'layout_ext'=> '.'.$format.'.php'
				);
				$class = $request->get('config')->class_map->view;
				$theme = $request->newInstance($class, $options);

				// Asssign main content.
				$theme->placeholder('content')->append($request->getResponse()->getBody());

				// Get site config.
				$config = $request->get('config');

				// Get site document.
				$document = $request->get('document');

				if (!isset($document->title)) {
					$document->title = $config->sitename;
				}

				foreach ($document as $key => &$val) {
					$theme->bind($key, $val);
				}
				$theme->assign('sitename', $config->sitename);
				$theme->assign('template', $config->template);

				// Init & Send Response.
				$response = new Response($theme, 200, $headers);
				$response
					->setHeader('Content-Type', $document->mime.'; charset='.$document->charset)
					->setHeader('X-Powered-By', 'Comvi/'.VERSION);
				if (!empty($document->mdate)) {
					// Set modified date.
					$response->setHeader('Last-Modified', $document->mdate);
				}

				$request->setResponse($response);
			//}
		});
	}
}
