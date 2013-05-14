<?php
	return array(
		'sitename'	=> 'Comvi',
		'template'	=> 'default',
		'static_url'=> 'http://localhost/comvi/static/',
		// List of Supported Timezones:
		// http://php.net/manual/en/timezones.php
		'timezone'	=> 'Etc/GMT+0',
		'force_ssl'	=> false,
		'class_map'	=> array(
			'uri'		=> 'Comvi\\Core\\URI',
			'router'	=> 'Comvi\\Core\\Routers',
			'request'	=> 'Comvi\\Core\\Request',
			'document_html'	=> 'Comvi\\Core\\Document_HTML',
			'view'		=> 'Comvi\\Core\\View',
			//'profiler'	=> 'Comvi\\Core\\Profiler'
		),
		'config_map'	=> array('preload', 'document', 'route', 'i18n', 'session', 'auth', 'openid', 'module', 'database', 'service_manager')
	);
?>