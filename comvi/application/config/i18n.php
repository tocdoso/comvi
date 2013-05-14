<?php
	return array(
		'driver'	=> 'parameter', //ccTLD || subdomain || subdirectory || parameter

		'use_primary_language'	=> true, // true: en, false: en-US
		'preferred_mode'		=> null, // // Comvi\I18N\Router::REQUIRE_LANGUAGE || Comvi\I18N\Router::NOT_REQUIRE_LANGUAGE || NULL

		'default_language'		=> 'en',
		'supported_languages'	=> array('en')
	);
?>