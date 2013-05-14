<?php
	return array(
		//'mode'	=> Router::ROUTING_MODE_RAW,	// Router::ROUTING_MODE_RAW | Router::ROUTING_MODE_SEF
		'preferred_domain'	=> NULL,				// Router::WWW | Router::NONE_WWW | NULL
		'remove_index_file'	=> true,
		//'index_file' => 'index.php',
		'suffix'	=> '',
		'index'		=> '',
		'401'		=> '?controller=Error/Unauthorized',
		'404'		=> '?controller=Error/NotFound',
		'500'		=> '?controller=Error/ServerError'
	);
?>