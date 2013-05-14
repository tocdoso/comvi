<?php
namespace Comvi\ORM;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Event\Listeners\MysqlSessionInit;
use Gedmo\DoctrineExtensions;
use Gedmo\Tree\TreeListener;
use Gedmo\Timestampable\TimestampableListener;
//use Gedmo\Blameable\BlameableListener;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Translatable\TranslatableListener;

/**
 * The EntityManagerFactory class.
 *
 * @package		Comvi.ORM
 */
class EntityManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		// database configuration parameters
		$conn = $serviceLocator->get('config')->database->toArray();

		// ensure standard doctrine annotations are registered
		AnnotationRegistry::registerFile(
			PATH_SYSTEM.'Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
		);

		// Second configure ORM
		// globally used cache driver, in production use APC or memcached
		$cache = new ArrayCache;
		// standard annotation reader
		$annotationReader = new AnnotationReader;
		$this->cachedAnnotationReader = new CachedReader(
			$annotationReader, // use reader
			$cache // and a cache driver
		);
		// create a driver chain for metadata reading
		$driverChain = new DriverChain;
		// load superclass metadata mapping only, into driver chain
		// also registers Gedmo annotations.NOTE: you can personalize it
		DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
			$driverChain, // our metadata driver chain, to hook into
			$this->cachedAnnotationReader // our cached annotation reader
		);

		// now we want to register our application entities,
		// for that we need another metadata driver used for Entity namespace
		$annotationDriver = new AnnotationDriver(
			$this->cachedAnnotationReader // our cached annotation reader
		);
		// NOTE: driver for application Entity can be different, Yaml, Xml or whatever
		// register annotation driver for our application Entity namespace
		$driverChain->addDriver($annotationDriver, 'Entity');

		// general ORM configuration
		$config = new Configuration;
		$config->setProxyDir(sys_get_temp_dir());
		$config->setProxyNamespace('Proxy');
		//$config->setAutoGenerateProxyClasses(false); // this can be based on production config.
		// register metadata driver
		$config->setMetadataDriverImpl($driverChain);
		// use our allready initialized cache driver
		//$config->setMetadataCacheImpl($cache);
		//$config->setQueryCacheImpl($cache);

		// Third, create event manager and hook prefered extension listeners
		$evm = new EventManager;
		// gedmo extension listeners

		// tree
		$treeListener = new TreeListener;
		$treeListener->setAnnotationReader($this->cachedAnnotationReader);
		$evm->addEventSubscriber($treeListener);

		// loggable, not used in example
		//$loggableListener = new Gedmo\Loggable\LoggableListener;
		//$loggableListener->setAnnotationReader($this->cachedAnnotationReader);
		//$evm->addEventSubscriber($loggableListener);

		// timestampable
		$timestampableListener = new TimestampableListener;
		$timestampableListener->setAnnotationReader($this->cachedAnnotationReader);
		$evm->addEventSubscriber($timestampableListener);

		// blameable
		/*$blameableListener = new BlameableListener;
		$blameableListener->setAnnotationReader($this->cachedAnnotationReader);
		$blameableListener->setUserValue('MyUsername'); // determine from your environment
		$evm->addEventSubscriber($blameableListener);*/

		// sluggable
		$sluggableListener = new SluggableListener;
		$sluggableListener->setTransliterator(array('MyGedmo\Sluggable\Util\Urlizer', 'transliterate'));
		$evm->addEventSubscriber($sluggableListener);

		// translatable
		$locale			= $serviceLocator->get('document')->language;
		$default_locale	= $serviceLocator->get('config')->i18n->default_language;
		$translatableListener = new TranslatableListener;
		// current translation locale should be set from session or hook later into the listener
		// most important, before entity manager is flushed
		$translatableListener->setTranslatableLocale($locale);
		$translatableListener->setDefaultLocale($default_locale);
		$translatableListener->setTranslationFallback(true); // default is false
		//$translatableListener->setAnnotationReader($this->cachedAnnotationReader);
		$evm->addEventSubscriber($translatableListener);

		// sortable, not used in example
		//$sortableListener = new Gedmo\Sortable\SortableListener;
		//$sortableListener->setAnnotationReader($this->cachedAnnotationReader);
		//$evm->addEventSubscriber($sortableListener);

		// mysql set names UTF-8 if required
		$evm->addEventSubscriber(new MysqlSessionInit);
		// Finally, create entity manager
		$em = EntityManager::create($conn, $config, $evm);

		return $em;
	}
}
