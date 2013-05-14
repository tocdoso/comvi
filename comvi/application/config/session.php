<?php
	return array(
		'cache_expire'	=> 30,				// Specifies time-to-live for cached session pages in minutes.
		'name'			=> '0b1u7OGkfjQ1dlBn',	// Specifies the name of the session which is used as cookie name.
		'use_cookies'		=> true,		// Specifies whether the module will use cookies to store the session id.
		'cookie_httponly'	=> false,		// Marks the cookie as accessible only through the HTTP protocol.
		'cookie_secure'		=> false,		// Specifies whether cookies should only be sent over secure connections.
		'cookie_domain'		=> '',			// Specifies the domain to set in the session cookie.
		'cookie_path'		=> '/',			// Specifies path to set in the session cookie.
		//'cookie_lifetime'					// Specifies the lifetime of the cookie in seconds which is sent to the browser.
		//'entropy_length'					// Specifies the number of bytes which will be read from the file specified in entropy_file.
		//'entropy_file'
		//'gc_maxlifetime
		//'gc_divisor'
		//'gc_probability'
		//'hash_bits_per_character'
		//'remember_me_seconds'				// Specifies how long to remember the session before clearing data.
		//'save_path'						// Defines the argument which is passed to the save handler.
	);
?>