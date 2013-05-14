<?php
	return array(
		'^.*'	=> array(
			'menu'			=> 'com=Menu&root_id=1',
			'breadcrumbs'	=> 'com=Menu&task=Breadcrumbs',
			//'footer_links'	=> 'com=Menu&controller=Items&layout=footer_links',
			'footer_links'	=> 'com=Language&task=List&layout=links'
		),
		'^\?lang=([^&]*)$'	=> array(
			'slideshow'		=> 'com=Slideshow&lang=$1'
		)
	);
?>