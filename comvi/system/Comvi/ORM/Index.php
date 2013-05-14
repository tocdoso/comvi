<?php
namespace Comvi\ORM;

/**
 * Comvi/ORM bootstrap class.
 */
class Index extends \Comvi\Core\Package
{
	public function index()
	{
		$this->service_manager->setFactory('EntityManager', 'Comvi\\ORM\\EntityManagerFactory');
		$this->service_manager->setAlias('em', 'EntityManager');
	}
}
