<?php
use Comvi\Core\Router;

	return array(
		'driver'	=> 'ccTLD', //ccTLD || subdomain || subdirectory || parameter

		'use_primary_language'	=> true,								// true: en, false: en-US
		'preferred_mode'		=> Comvi\I18N\Router::REQUIRE_LANGUAGE,	// Comvi\I18N\Router::REQUIRE_LANGUAGE || Comvi\I18N\Router::NOT_REQUIRE_LANGUAGE || NULL

		'default_language' => 'en',
		//'supported_languages'	=> array('en', 'vi'),
		'supported_languages'	=> array(
			'localhost.com'		=> 'en',
			'www.localhost.com'		=> 'en',
			'localhost.com.vn'	=> 'vi',
			'www.localhost.com.vn'	=> 'vi'
		)
	);
?>