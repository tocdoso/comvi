<?php
namespace Comvi\Core;
use Comvi\Core\Exception\HttpNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The DocumentFactory class.
 *
 * @package		Comvi.Core
 */
class DocumentFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$options = $serviceLocator->get('config')->document->toArray();
		$format	 = $serviceLocator->get('current_url')->getVar('format', 'html');
		$class	 = $serviceLocator->get('config')->class_map->{'document_'.$format};
		if ($class === null) {
			$format = 'html';
			$class = $serviceLocator->get('config')->class_map->{'document_html'};
			//throw new HttpNotFoundException();
		}

		if ($format === 'html') {
			$options['static_url'] = $serviceLocator->get('config')->static_url;
		}

		return new $class($options);
	}
}
