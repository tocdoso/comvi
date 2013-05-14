<?php
	return array(
		'instance' => array( 
			'alias' => array( 
				//'config'	=> 'Comvi\\Core\\Config',
				//'doc'		=> 'Comvi\\Core\\Document',
				'uri'		=> 'Comvi\\Core\\URI',
				'router'	=> 'Comvi\\Core\\Router',
				'request'	=> 'Comvi\\Core\\Request',
				'document_html'	=> 'Comvi\\Core\\Document_HTML'
				//'benchmark'	=> 'Comvi\\Core\\Helper\\Benchmark'
			),
			/*'Comvi\\Core\\Config' => array(
				'parameters' => array(
					'array' => array(),
					//'name'	=> 'preload'
				)
			)*/
		),
		'definition' => array(
			'compiler' => array(/* @todo compiler information */),
			'runtime'  => array(/* @todo runtime information */),
			'class' => array(
				'Comvi\\Core\\Config' => array(
					//'setDatabaseAdapter' => array('required' => true)
				),
				'Comvi\\Core\\Request' => array(
				)
			)
		)
	);
?>