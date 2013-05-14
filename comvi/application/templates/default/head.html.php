<title><?php echo $title;?></title>
<?php
	$this->translator
		->addTranslationFilePattern('PhpArray', __DIR__, 'languages'.DS.'%s.php', 'theme');

	$this->document
		->addMobileMeta()
		->addFavicon('img/favicon.ico')
		->addIcon('img/apple-touch-icon-57x57.png')
		->addIPhoneIcon('img/apple-touch-icon-57x57.png')
		->addIPhone4Icon('img/apple-touch-icon-114x114.png')
		->addIPadIcon('img/apple-touch-icon-72x72.png')
		->addIPad3Icon('img/apple-touch-icon-144x144.png')
		->addStyle('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css')
		->addStyle($static_url.$template.'/css/html5.css')
		->addStyle($static_url.$template.'/css/style.css')
		//->addScript($static_url.'js/html5.js')
		->flushHeadElements()
		->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js')
		->addScript('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/js/bootstrap.min.js');
?>
<!--[if lt IE 9]><script src="<?php echo $static_url.$template;?>/js/html5.js"></script><![endif]-->
