<?php
namespace User;
use Comvi\ORM\EntityManagerAwareInterface;
use Comvi\ORM\EntityManagerAwareTrait;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Setup Controller
 */
class Setup implements EntityManagerAwareInterface
{
	use EntityManagerAwareTrait;

	public function actionIndex()
	{
		$tool = new SchemaTool($this->em);
		$classes = array(
			$this->em->getClassMetadata('Entity\User'),
			$this->em->getClassMetadata('Entity\Role')
		);
		$tool->createSchema($classes);

		return 'User + Group (Many-To-Many, Bidirectional)';
	}
}
?>