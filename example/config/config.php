<?php

// This is a sample configuration file
$socketDir = '/var/run/php-fpm';

$liveMemory = 32;


// By deploying each version of an app into its own directory,
// this allows a unique socket to be used per deploy.
// e.g. if the app is deployed into the directory /home/appname/${commitSHA}
//
$fullSocketPath = $socketDir."/php-fpm-example-".basename(dirname(__DIR__)).".sock";

$default = [
    //global/default variables go here.
    'nginx.sendFile' => 'off',
    'mysql.charset' => 'utf8mb4',
    'mysql.collation' => 'utf8mb4_unicode_ci',
];

$centos = [
    'nginx.log.directory' => '/var/log/nginx',
    'nginx.root.directory' => '/usr/share/nginx',
    'nginx.conf.directory' => '/etc/nginx',
    'nginx.run.directory ' => '/var/run',
    'nginx.user' => 'nginx',
    'nginx.sendFile' => 'on',
    
    'example.root.directory' => dirname(__DIR__),
    
    'phpfpm.www.maxmemory' => "".$liveMemory."M",
    'phpfpm.user' => 'intahwebz',
    'phpfpm.group' => 'www-data',
    'phpfpm.socket.directory' => $socketDir,
    'phpfpm.conf.directory' => '/etc/php-fpm.d',
    'phpfpm.pid.directory' => '/var/run/php-fpm',
    
    //
    'phpfpm.fullsocketpath' => $fullSocketPath,

    'php.conf.directory' => '/etc/php',
    'php.log.directory' => '/var/log/php',
    'php.errorlog.directory' => '/var/log/php',
    'php.session.directory' => '/var/lib/php/session',
];



$dev = [];

// Dev mode should be run with less memory than live, to make
// out of memory issues more likely to show up on dev than live
$dev['phpfpm.www.maxmemory'] = "".($liveMemory - 10)."M";