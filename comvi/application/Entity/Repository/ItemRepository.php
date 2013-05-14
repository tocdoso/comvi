<?php
namespace Entity\Repository;

use Comvi\Core\URI;
use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Gedmo\Translatable\TranslatableListener;

class ItemRepository extends NestedTreeRepository
{
	protected static function toFlat($arrayTree, $options = array())
	{
        $default = array(
            'sep'	 => '&nbsp;',
            'repeat' => 4,
            'path'	 => ''
        );
        $options = array_merge($default, $options);

		$els = array();
		foreach ($arrayTree as $opts) {
			$els[$opts['id']] = $options['path'] . str_repeat($options['sep'], $options['repeat'] * $opts['level']) . $opts['name'];

			if (isset($opts['__children']) && is_array($opts['__children']) && sizeof($opts['__children'])) {
				$r = static::toFlat($opts['__children'], $options);

				foreach($r as $id => $title) {
					$els[$id] = $title;
				}
			}
		}

		return $els;
	}

	public function childrenFlat($node = null, $direct = false, $options = array())
	{
        $arrayTree = $this->childrenHierarchy($node, $direct);

        return static::toFlat($arrayTree, $options);
	}

    public function getNodesHierarchyQuery($node = null, $direct = false, array $options = array(), $includeNode = false)
    {
		$query = parent::getNodesHierarchyQuery($node, $direct, $options, $includeNode);
		$query->setHint(
			Query::HINT_CUSTOM_OUTPUT_WALKER,
			'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
		);
		// locale
		/*$query->setHint(
			\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
			'en', // take locale from session or request etc.
		);*/
		// fallback
		$query->setHint(
			TranslatableListener::HINT_FALLBACK,
			1 // fallback to default values in case if record is not translated
		);

		return $query;
    }

	public function findURL($url)
	{
		foreach ($this->getChildren() as $child) {
			if ($url->equal(new URI($child->getURL()))) {
				return $child;
			}
		}

		return null;
	}

	/*public function findURL($url)
	{
		foreach ($this->getChildren(null, true) as $child) {
			if (($result = $child->findURL($url)) !== null) {
				return $result;
			}
		}

		return null;
	}*/
}