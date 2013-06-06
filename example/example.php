<?php

require_once "../vendor/autoload.php";

use Intahwebz\Configurator\Configurator;


define('FLICKR_KEY', 12345);

$filesToGenerate = array(
	'input/nginx.conf.php'				=>	'output/nginx.conf',
	'input/sitename.nginx.conf.php'		=>	'output/sitename.nginx.conf',
	'input/sitename.php-fpm.conf.php'	=>	'output/sitename.php-fpm.conf',
	'input/my.cnf.php' 					=>	'output/my.cnf',
	'input/php-fpm.conf.php'			=>	'output/php-fpm.conf',
);


$environment = 'amazonec2';

if ($argc >= 2){
	$environmentRequired = $argv[1];
	$allowedVars = array(
		'amazonec2',
		'macports',
	);
	if (in_array($environmentRequired, $allowedVars) == true){
		$environment = $environmentRequired;
	}
}
else{
	echo "Defaulting to [".$environment."] environment";
}

$configurator = new Configurator();
$configurator->addConstant('FLICKR_KEY');
$configurator->addConfig($environment, "input/deployConfig.ini");

$configurator->addConfigValue('sitename', 'example'); //Not example.com


foreach($filesToGenerate as $inputFilename => $outputFilename){
	$configFile = $configurator->configurate($inputFilename);
	$fileHandle = fopen($outputFilename, "w");

	if($fileHandle === FALSE){
		echo "Failed to read [".$outputFilename."] for writing.";
		exit(-1);
	}

	fwrite($fileHandle, $configFile);
	fclose($fileHandle);
}



?>