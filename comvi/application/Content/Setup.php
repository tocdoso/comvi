<?php
namespace Content;
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
			$this->em->getClassMetadata('Entity\\Content\\Category'),
			$this->em->getClassMetadata('Entity\\Content\\CategoryTranslation'),
			$this->em->getClassMetadata('Entity\\Content\\Tag'),
			$this->em->getClassMetadata('Entity\\Content\\TagTranslation'),
			$this->em->getClassMetadata('Entity\\Content'),
			$this->em->getClassMetadata('Entity\\ContentTranslation')
		);
		$tool->createSchema($classes);

		return 'Category, Tag + Content (Many-To-Many, Bidirectional)';
	}
}
?>