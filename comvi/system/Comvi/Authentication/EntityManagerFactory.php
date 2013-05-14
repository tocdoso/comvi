<?php
namespace Comvi\Authentication;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container as SessionContainer; 
use Gedmo\Blameable\BlameableListener;

/**
 * The EntityManagerFactory class.
 *
 * @package		Comvi.Authentication
 */
class EntityManagerFactory extends \Comvi\ORM\EntityManagerFactory
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$this->em = parent::createService($serviceLocator);

		$blameableListener = new BlameableListener;
		$blameableListener->setAnnotationReader($this->cachedAnnotationReader);
		$blameableListener->setUserValue($this->getUser()); // determine from your environment
		$this->em->getEventManager()->addEventSubscriber($blameableListener);

		return $this->em;
	}

    public function getUser()
	{
		$user = new SessionContainer('user');

		if ($user->id) {
			return $this->em->getRepository('Entity\\User')->find((int) $user->id);
		}

		return null;
	}
}
