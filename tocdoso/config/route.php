<?php
	return array(
		'preferred_domain'	=> Comvi\Core\Router::WWW,			// Router::WWW | Router::NONE_WWW | NULL
		//'remove_index_file'	=> false,
		//'suffix'	=> 'html',
		//'index_filename'	=> 'home',

		'mode'	=> Comvi\Routing\Router::ROUTING_MODE_SEF,	// Router::ROUTING_MODE_RAW | Router::ROUTING_MODE_SEF

		'rules'	=> array(
			array(
				'pattern'	=> '^$',
				'target'	=> 'Content/Controller/index'
			),
			array(
				'pattern'	=> '^([a-z-]+)$',
				'target'	=> 'Content/Controller/$1'
			)
		),
		'reverse_rules'	=> array(
			array(
				'pattern'	=> '^Content/Controller/index$',
				'target'	=> ''
			),
			array(
				'pattern'	=> '^Content/Controller/([a-z-]+)$',
				'target'	=> '$1'
			)
			/*array(
				'pattern'	 => '^Content?task=Add$',
				'target' => 'content/add'
			)*/
		)
	);
?>