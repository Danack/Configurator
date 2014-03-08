<?php

require_once "../vendor/autoload.php";

use Intahwebz\Configurator\Configurator;


define('FLICKR_KEY', 12345);

$filesToGenerate = array(
    'input/nginx.conf.php'              => 'output/nginx.conf',
    'input/sitename.nginx.conf.php'     => 'output/sitename.nginx.conf',
    'input/sitename.php-fpm.conf.php'   => 'output/sitename.php-fpm.conf',
    'input/my.cnf.php'                  => 'output/my.cnf',
    'input/php-fpm.conf.php'            => 'output/php-fpm.conf',
);


$environment = 'amazonec2';

$knownEnvironments = array(
    'amazonec2',
    'macports',
);

if ($argc >= 2){
    $environmentRequired = $argv[1];
    if (in_array($environmentRequired, $knownEnvironments) == true){
        $environment = $environmentRequired;
    }
    else {
        echo "Unknown environment, please run with ". implode(', ', $knownEnvironments);
        exit(-1);
    }
}
else{
    echo "Defaulting to [".$environment."] environment";
    echo "Environment not set, please run with ". implode(', ', $knownEnvironments);
    exit(-1);
}

$configurator = new Configurator();
//Add a constant - this would normally be included from a config file that 
//is outside of version control, because adding secrets to version control is bad 
$configurator->addConstant('FLICKR_KEY');

//Add the config from a file, specifying which environment to use
$configurator->addConfig($environment, "input/deployConfig.php");

//Add a value with a name.
$configurator->addConfigValue('sitename', 'example'); //Not example.com


//Generate the files
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
