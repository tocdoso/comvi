<?php
namespace Comvi\ORM;
use Doctrine\ORM\EntityManager;

/**
 * Declare EntityManager Aware trait.
 *
 * @package		Comvi.ORM
 */
trait EntityManagerAwareTrait
{
	protected $em;

	public function setEntityManager(EntityManager $em)
	{
		$this->em = $em;
	}
}
