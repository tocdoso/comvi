<?php
	return array(
		'driver'	=> 'acl',
		'groups' => array(
			array(
				'name'	 => 'unregistered',
				'parent' => array(),
				'allow'	 => array('view')
			),
			array(
				'name'	 => 'registered',
				'parent' => array('unregistered'),
				'allow'	 => array()
			),
			array(
				'name'	 => 'author',
				'parent' => array('registered'),
				'allow'	 => array('add', 'edit_own', 'delete_own')
			),
			array(
				'name'	 => 'editor',
				'parent' => array('author'),
				'allow'	 => array('edit', 'delete')
			),
			array(
				'name'	 => 'publisher',
				'parent' => array('editor'),
				'allow'	 => array('options')
			),
			array(
				'name'	 => 'administrator',
				'parent' => array('publisher'),
				'allow'	 => array('install', 'uninstall')
			),
			array(
				'name'	 => 'root',
				'parent' => array(),
				'allow'	 => array(null)
			)
		)
	);
?>