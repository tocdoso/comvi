<?php
namespace Comvi\ORM;
use Doctrine\ORM\EntityManager;

/**
 * Declare EntityManager Aware interface.
 *
 * @package		Comvi.ORM
 */
interface EntityManagerAwareInterface
{
	public function setEntityManager(EntityManager $em);
}
