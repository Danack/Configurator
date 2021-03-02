<?php

use Configurator\ConfiguratorException;
use ConfiguratorTest\ExampleConfig;
// This is a sample configuration file

//global variables go here.
$default = array(
    ExampleConfig::FOO => 'default foo',
    ExampleConfig::BAR => 'default bar',
    ExampleConfig::DB => [
        'port' => 3306,
        'host' => '127.0.0.1',
        'auth' => false
    ],
);

$local = array(
    ExampleConfig::FOO => 'local foo',
);


