<?php

$autoloader = require(__DIR__.'/../vendor/autoload.php');

$autoloader->add('Configurator', [__DIR__]);

require_once __DIR__ . "/fixtures/app_data/ExampleConfig.php";
