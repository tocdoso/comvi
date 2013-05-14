<?php
namespace Comvi\Navigation;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Comvi\Core\URI;

/**
 * The NavigationFactory class.
 *
 * @package		Comvi.Core
 */
class NavigationFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		//$serviceLocator->get('entity_manager')->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
		$em			 = $serviceLocator->get('entity_manager');
		$current_url = $serviceLocator->get('current_url');
		$home_name	 = $serviceLocator->get('translator')->translate('Home', 'default', $serviceLocator->get('document')->language);
		$home_url	 = new URI('');
		$serviceLocator->get('router')->parse($home_url);
		return new Navigation($em, $current_url, $home_url, $home_name);

		//$items = $repo->childrenHierarchy();
		//\Doctrine\Common\Util\Debug::dump($repo->getChildren(null, true));die();
		/*foreach ($repo->getChildren(null, true) as $children) {
			\Doctrine\Common\Util\Debug::dump($children->getChildren());
		}
		
		die();
		$navigation = new ItemContainer($items);

		return $navigation;*/
	}
}
