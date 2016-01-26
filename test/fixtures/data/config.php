<?php

use Configurator\ConfiguratorException;

// This is a sample configuration file

//global variables go here.
$default = array(
    'app_name' => 'test',
    'mysql_charset'   => 'utf8mb4',
    'mysql_collation' => 'utf8mb4_unicode_ci',
    'php_memory_limit' => '16M',
    'cache_setting' => 'cache_time',
);

$amazonec2 = array(
    'nginx_log_directory'  => '/var/log/nginx',
    'nginx_root_directory' => '/usr/share/nginx',
    'nginx_conf_directory' => '/etc/nginx',
    'nginx_run_directory ' => '/var/run',
    'nginx_user'           => 'nginx',
    
    'sitename_chroot_directory' => '/home/intahwebz/current',
    'sitename_root_directory'   => '/home/intahwebz/current',
    'sitename_cache_directory'  => '/home/intahwebz/current/var/cache',
    
    'phpfpm_socket'             => '/var/run/php-fpm',
    'phpfpm_www_maxmemory'      => '16M',
    'phpfpm_images_maxmemory'   => '48M',
    'phpfpm_user'               => 'intahwebz',
    'phpfpm_group'              => 'www-data',
    'phpfpm_socket_directory'   => '/var/run/php-fpm',
    'phpfpm_conf_directory'     => '/etc/php-fpm.d',
    'phpfpm_pid_directory'      => '/var/run/php-fpm',
    
    'php_log_directory'      => '/var/log/php-fpm',
    'php_errorlog_directory' => '/var/log/php-fpm',
    'php_session_directory'  => '/var/lib/php/session',
    
    'mysql_casetablenames'   => '0',
    'mysql_datadir'          => '/var/lib/mysql/',
    'mysql_socket'           => '/var/lib/mysql/mysql.sock',
    'mysql_log_directory'    => '/var/log',
);

$vagrant = array(
    'nginx_log_directory'  => '/var/log/nginx',
    'nginx_root_directory' => '/usr/share/nginx',
    'nginx_conf_directory' => '/etc/nginx',
    'nginx_run_directory ' => '/var/run',
    'nginx_user'           => 'www-data',
    
    'sitename_chroot_directory' => '/home/intahwebz/current',
    'sitename_root_directory'   => '/home/intahwebz/intahwebz/',
    'sitename_cache_directory'  => '/home/intahwebz/intahwebz/var/cache',
    
    'phpfpm_socket'             => '/var/run/php-fpm',
    'phpfpm_www_maxmemory'      => '16M',
    'phpfpm_images_maxmemory'   => '48M',
    'phpfpm_user'               => 'intahwebz',
    'phpfpm_group'              => 'www-data',
    'phpfpm_socket_directory'   => '/var/run/php-fpm',
    'phpfpm_conf_directory'     => '/etc/php/php-fpm.d',
    'phpfpm_pid_directory'      => '/var/run/php-fpm',
    
    'php_conf_directory' => '/etc/php',
    'php_log_directory' => '/var/log/php',
    'php_errorlog_directory' => '/var/log/php',
    'php_session_directory' => '/var/lib/php/session',
    
    'mysql_casetablenames'  => '0',
    'mysql_datadir'         => '/var/lib/mysql',
    'mysql_socket'          => '/var/run/mysqld/mysqld.sock',
    'mysql_log_directory'   => '/var/log',
);

$macports = array(
    'nginx_log_directory'  => '/opt/local/var/log/nginx',
    'nginx_root_directory' => '/opt/local/share/nginx',
    'nginx_conf_directory' => '/opt/local/etc/nginx',
    'nginx_run_directory'  => '/opt/local/var/run',
    'nginx_user'           => '_www',
    
    'sitename_chroot_directory' => '/documents/projects/intahwebz/intahwebz',
    'sitename_root_directory'   => '/documents/projects/intahwebz/intahwebz',
    'sitename_cache_directory'  => '/documents/projects/intahwebz/intahwebz/var/cache',
    
    'phpfpm_socket'           => '/opt/local/var/run/php54',
    'phpfpm_www_maxmemory'    => '16M',
    'phpfpm_images_maxmemory' => '48M',
    'phpfpm_user'             => '_www',
    'phpfpm_group'            => '_www',
    'phpfpm_pid_directory'    => '/opt/local/var/run/php54',
    'phpfpm_conf_directory'   => '/opt/local/etc/php54/sites-enabled',

    'php_conf_directory'      => '/opt/local/etc/php54',
    'phpfpm_socket_directory' => '/opt/local/var/run/php54',
    'php_log_directory'       => '/opt/local/var/log/php54',
    'php_errorlog_directory'  => '/opt/local/var/log/php54',
    'php_session_directory'   => '',
    
    'mysql_socket'          => '/documents/projects/intahwebz/intahwebz/var/mysql/mysql.sock',
    'mysql_casetablenames'  => '2',
    'mysql_datadir'         => '/opt/local/var/db/mysql55/',
    'mysql_log_directory'   => '/opt/local/var/log/mysql55',
    'mysql_run_dir'         => '/opt/local/var/run/mysql55',
);


$evaluate = function ($config, $environment) {
    if (array_key_exists('app_name', $config) === false) {
        throw new ConfiguratorException("app.name isn't set for environment '$environment'.");
    }

    return [
        'app_name_uppercase' => strtoupper($config['app_name'])
    ];
};
