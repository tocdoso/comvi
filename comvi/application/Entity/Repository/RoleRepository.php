<?php
namespace Entity\Repository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class RoleRepository extends NestedTreeRepository
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

				foreach($r as $id => $name) {
					$els[$id] = $name;
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
}