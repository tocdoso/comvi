<?php
namespace Comvi\Authentication;
use Comvi\Core\Package;
//use Gedmo\Blameable\BlameableListener;

/**
 * Comvi/Authentication bootstrap class.
 */
class Index extends Package
{
	public function index()
	{
		$this->service_manager->setFactory('Authentication', 'Comvi\\Authentication\\AuthenticationFactory');
		$this->service_manager->setAlias('auth', 'Authentication');
		//$this->service_manager->setFactory('User', 'Comvi\\Authentication\\UserFactory');
		$this->service_manager->setFactory('EntityManager', 'Comvi\\Authentication\\EntityManagerFactory');
	}
}
