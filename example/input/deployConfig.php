<?php

// This is a sample configuration file

//global variables go here.
$default = array(
    'mysql.charset'   => 'utf8mb4',
    'mysql.collation' => 'utf8mb4_unicode_ci',
);



$amazonec2 = array(
    'nginx.log.directory'  => '/var/log/nginx',
    'nginx.root.directory' => '/usr/share/nginx',
    'nginx.conf.directory' => '/etc/nginx',
    'nginx.run.directory ' => '/var/run',
    'nginx.user'           => 'nginx',
    
    'sitename.chroot.directory' => '/home/intahwebz/current',
    'sitename.root.directory'   => '/home/intahwebz/current',
    'sitename.cache.directory'  => '/home/intahwebz/current/var/cache',
    
    'phpfpm.socket'             => '/var/run/php-fpm',
    'phpfpm.www.maxmemory'      => '16M',
    'phpfpm.images.maxmemory'   => '48M',
    'phpfpm.user'               => 'intahwebz',
    'phpfpm.group'              => 'www-data',
    'phpfpm.socket.directory'   => '/var/run/php-fpm',
    'phpfpm.conf.directory'     => '/etc/php-fpm.d',
    'phpfpm.pid.directory'      => '/var/run/php-fpm',
    
    'php.log.directory'      => '/var/log/php-fpm',
    'php.errorlog.directory' => '/var/log/php-fpm',
    'php.session.directory'  => '/var/lib/php/session',
    
    'mysql.casetablenames'   => '0',
    'mysql.datadir'          => '/var/lib/mysql/',
    'mysql.socket'           => '/var/lib/mysql/mysql.sock',
    'mysql.log.directory'    => '/var/log',
);

$vagrant = array(
    'nginx.log.directory'  => '/var/log/nginx',
    'nginx.root.directory' => '/usr/share/nginx',
    'nginx.conf.directory' => '/etc/nginx',
    'nginx.run.directory ' => '/var/run',
    'nginx.user'           => 'www-data',
    
    'sitename.chroot.directory' => '/home/intahwebz/current',
    'sitename.root.directory'   => '/home/intahwebz/intahwebz/',
    'sitename.cache.directory'  => '/home/intahwebz/intahwebz/var/cache',
    
    'phpfpm.socket'             => '/var/run/php-fpm',
    'phpfpm.www.maxmemory'      => '16M',
    'phpfpm.images.maxmemory'   => '48M',
    'phpfpm.user'               => 'intahwebz',
    'phpfpm.group'              => 'www-data',
    'phpfpm.socket.directory'   => '/var/run/php-fpm',
    'phpfpm.conf.directory'     => '/etc/php/php-fpm.d',
    'phpfpm.pid.directory'      => '/var/run/php-fpm',
    
    'php.conf.directory' => '/etc/php',
    'php.log.directory' => '/var/log/php',
    'php.errorlog.directory' => '/var/log/php',
    'php.session.directory' => '/var/lib/php/session',
    
    'mysql.casetablenames'  => '0',
    'mysql.datadir'         => '/var/lib/mysql',
    'mysql.socket'          => '/var/run/mysqld/mysqld.sock',
    'mysql.log.directory'   => '/var/log',
);

$macports = array(
    'nginx.log.directory'  => '/opt/local/var/log/nginx',
    'nginx.root.directory' => '/opt/local/share/nginx',
    'nginx.conf.directory' => '/opt/local/etc/nginx',
    'nginx.run.directory'  => '/opt/local/var/run',
    'nginx.user'           => '_www',
    
    'sitename.chroot.directory' => '/documents/projects/intahwebz/intahwebz',
    'sitename.root.directory'   => '/documents/projects/intahwebz/intahwebz',
    'sitename.cache.directory'  => '/documents/projects/intahwebz/intahwebz/var/cache',
    
    'phpfpm.socket'           => '/opt/local/var/run/php54',
    'phpfpm.www.maxmemory'    => '16M',
    'phpfpm.images.maxmemory' => '48M',
    'phpfpm.user'             => '_www',
    'phpfpm.group'            => '_www',
    'phpfpm.pid.directory'    => '/opt/local/var/run/php54',
    'phpfpm.conf.directory'   => '/opt/local/etc/php54/sites-enabled',

    'php.conf.directory'      => '/opt/local/etc/php54',
    'phpfpm.socket.directory' => '/opt/local/var/run/php54',
    'php.log.directory'       => '/opt/local/var/log/php54',
    'php.errorlog.directory'  => '/opt/local/var/log/php54',
    'php.session.directory'   => '',
    
    'mysql.socket'          => '/documents/projects/intahwebz/intahwebz/var/mysql/mysql.sock',
    'mysql.casetablenames'  => '2',
    'mysql.datadir'         => '/opt/local/var/db/mysql55/',
   'mysql.log.directory'    => '/opt/local/var/log/mysql55',
    'mysql.run.dir'         => '/opt/local/var/run/mysql55',
);