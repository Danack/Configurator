<?php

/**
 * This file holds all environment values for all the environment settings choosable.
 */

use ExampleApp\AppConfig;

$default = [
    'nginx_sendFile' => 'off',
    'app_name' => 'blog',
    'phpfpm_www_maxmemory' => '16M',
    AppConfig::FILE_STORAGE => 'file.storage.s3'
];

$socketDir = '/var/run/php-fpm';

$centos = [
    'nginx_log_directory' => '/var/log/nginx',
    'nginx_root_directory' => '/usr/share/nginx',
    'nginx_conf_directory' => '/etc/nginx',
    'nginx_run_directory' => '/var/run',
    'nginx_user' => 'nginx',
    'nginx_sendFile' => 'off',

    'app_root_directory' => dirname(__DIR__),

    'phpfpm_maxmemory' => '16M',
    'phpfpm_user' => 'blog',
    'phpfpm_group' => 'www-data',
    'phpfpm_socket_directory' => $socketDir,
    'phpfpm_conf_directory' => '/etc/php-fpm.d',
    'phpfpm_pid_directory' => '/var/run/php-fpm',

    'phpfpm_fullsocketpath' => $socketDir."/php-fpm-blog-".basename(dirname(__DIR__)).".sock",

    'php_conf_directory' => '/etc/php',
    'php_log_directory' => '/var/log/php',
    'php_errorlog_directory' => '/var/log/php',
    'php_session_directory' => '/var/lib/php/session',
];

// Centos guest duplicates Centos production mostly
$centos_guest = $centos;

// But send file doesn't work in vagrant on virtualBox
$centos_guest['nginx_sendFile'] = 'off'; 


$dev = [
    AppConfig::SCRIPT_PACKING => true,
    AppConfig::CACHING_SETTING => 'caching.disable',
];

$live = [
    AppConfig::SCRIPT_PACKING => true,
    AppConfig::CACHING_SETTING => 'caching.time'
]; 


// Anyone doing UX testing needs to have the scripts packed together
// to avoid slow UI responses
$uxtesting[AppConfig::SCRIPT_PACKING] = true;

// Sometimes you just want to force a setting to a particular value
// without figuring out where it is being set. Setting it in an 
// override setting will set it for all environments.
//$override[AppConfig::SCRIPT_PACKING] = false;

