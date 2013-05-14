<?php
namespace Comvi\Core;

use Exception;
use Comvi\Core\Exception\HttpException;
use Comvi\Core\Exception\HttpFoundException;
use Comvi\Core\Exception\HttpUnauthorizedException;
use Comvi\Core\Exception\HttpNotFoundException;
use Zend\ServiceManager\Config as ServiceManagerConfig;
use Zend\EventManager\EventManagerInterface;

/**
 * The Application class is used to create and manage resources.
 *
 * @package		Comvi.Core
 */
class Application implements ServiceManagerAwareInterface
{
	use ServiceManagerAwareTrait;


	/**
	 * Creates the new Application object.
	 */
	public function __construct($options = array())
	{		
		$config = new Config($options, true);
		$config->setData();
		foreach ($config->config_map->toArray() as $name) {
			$config->{$name} = new Config(array());
			$config->{$name}->setData($name);
		}
		$this->service_manager = new ServiceManager(new ServiceManagerConfig($config->service_manager->toArray()));
		$this->service_manager->set('config', $config);

		$this->initialize();
	}

	public function initialize()
	{
		// Load 'preload Packages'.
		$packages = $this->getConfig()->preload->toArray();
		$this->getPackageManager()->load($packages);
	}

	public function run()
	{
		try {
			$current_url = $this->getCurrentURL();

			$this->getEventManager()->trigger('beforeRoute', $this);
			$this->getRouter()->parseAndCheck($current_url);
			$this->getEventManager()->trigger('afterRoute', $this);

			$this->getEventManager()->trigger('beforeDispatch', $this);
			$response = $this->getRequest()/*->setURI($current_url)*/->execute()->getResponse();
			$this->getEventManager()->trigger('afterDispatch', $this);
		}
		catch (HttpFoundException $e) {
			$url = $e->getMessage();
			$url = new URI($url);

			// Init & Send Response.
			$response = new Response;
			$response
				->setStatus(302)
				->setHeader('Location', (string) $url)
				->setHeader('Content-Type', $this->getDocument()->mime.'; charset='.$this->getDocument()->charset);
		}
		catch (HttpUnauthorizedException $e) {
			$format		= $this->getDocument()->type;
			$uri		= $this->getConfig()->route->{401};
			$uri		= new URI($uri);
			$uri->setVar('format', $format);

			$response = $this->getRequest()
				->setURI($uri)
				->setMethod('get')
				->execute()
				->getResponse();
			$response->setStatus(401);
		}
		catch (HttpNotFoundException $e) {
			$format		= $this->getDocument()->type;
			$uri		= $this->getConfig()->route->{404};
			$uri		= new URI($uri);
			$uri->setVar('format', $format);

			$response = $this->getRequest()
				->setURI($uri)
				->setMethod('get')
				->execute()
				->getResponse();
			$response->setStatus(404);
		}
		catch (HttpException $e) {
			return $this->getDefaultResponse($e->getMessage());
			$response = $e->getResponse();
		}
		catch (Exception $e) {
			//print_r($e);
			die($e->getMessage());
		}

		if ($response !== null) {
			$response->send();
		}
	}

	public function getServiceManager()
	{
		return $this->service_manager;
	}

    public function getEventManager()
    {
        return $this->getServiceManager()->get('event_manager');
    }

	public function getPackageManager()
	{
		return $this->getServiceManager()->get('package_manager');
	}

	public function getConfig()
	{
		return $this->getServiceManager()->get('config');
	}

	public function getDocument()
	{
		return $this->getServiceManager()->get('document');
	}

	public function getRequest()
	{
		return $this->getServiceManager()->get('request');
	}

	public function getCurrentURL()
	{
		return $this->getServiceManager()->get('current_url');
	}

	public function getRouter()
	{
		return $this->getServiceManager()->get('router');
	}
}
?>