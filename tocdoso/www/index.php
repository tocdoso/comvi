<?php
use Comvi\Core\Application;

require '../../comvi/init.php';

	// Init Application.
	$application = new Application(array(
		'start_time' => START_TIME,
		'start_mem'	 => START_MEM
	));

	// Execute Application.
	$application->run();

	// Uncomment bellow line for debug purpose.
	//print_r(Comvi\Core\Loader\FileLoader::getFilesLoaded());
?>