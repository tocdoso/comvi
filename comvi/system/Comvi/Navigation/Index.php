<?php
namespace Comvi\Navigation;
use Comvi\Core\Exception\HttpUnauthorizedException;
use Comvi\Core\Package;
use Comvi\Core\URI;

/**
 * Comvi/Core bootstrap class.
 */
class Index extends Package
{
	public function index()
	{
		$this->service_manager->setFactory('Navigation', 'Comvi\\Navigation\\NavigationFactory');

		$this->get('request')->getEventManager()->attach('beforeExecute', function ($e) {
			$request = $e->getTarget();
			$resource= $request->get('navigation')->getActiveItem();
			$resource= ($resource !== null) ? $resource->getId() : null;

			if (!$request->get('navigation')->getACL()->isDefaultRoleAllowed($resource, $request->getAction())) {
				throw new HttpUnauthorizedException();
			}
		});

		$this->get('request')->getEventManager()->attach('afterExecute', function ($e) {
			$request	= $e->getTarget();
			$instance	= $request->getControllerInstance();

			if (isset($instance->breadcrumbs)) {
				 $request->get('navigation')->addBreadcrumbs($instance->breadcrumbs);
			}
		});

		$this->get('request')->getEventManager()->attach('afterExecute', function ($e) {
			$request = $e->getTarget();
			$view	 = $request->getResponse()->getBody();

			foreach ($request->get('navigation')->getModules() as $module) {
				$uri		 = new URI($module->getModule()->getURL());
				$class		 = $this->get('config')->class_map->request;
				$sub_request = new $class($uri, 'get', false);
				$sub_request->setServiceManager($this->service_manager);
				$view->placeholder($module->getPosition())->append($sub_request->execute()->getResponse());
			}
		});
	}
}
