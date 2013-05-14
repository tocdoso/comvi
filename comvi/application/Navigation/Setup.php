<?php
namespace Navigation;
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
			//$this->em->getClassMetadata('Entity\ItemGroup'),
			$this->em->getClassMetadata('Entity\Item'),
			$this->em->getClassMetadata('Entity\ItemTranslation'),
			$this->em->getClassMetadata('Entity\Module'),
			//$this->em->getClassMetadata('Entity\Privilege')
		);
		$tool->createSchema($classes);

		return 'Item + Module + Privilege';
	}
}
?>