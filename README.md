Configurator
============

Generates config files from PHP based templates and PHP style config files.



## Generate config files


Source config file

```
    access_log  ${'nginx.log.directory'}/project.access.log requestTime;    
    error_log  ${'nginx.log.directory'}/project.error.log;
    root ${'project.root.directory'}/public;
```

Settings file:

```
<?php

$centos = [
    'nginx.log.directory' => '/var/log/nginx', 
    'project.root.directory' => '/home/project',
];

$windows = [
    'nginx.log.directory' => 'c:/nginx', 
    'project.root.directory' => 'c:/documents/project',
];

$dave = [
    'project.root.directory' => '/home/workdir/project',
]

```


configurate data/nginx.conf.php var/generated/nginx.conf centos,dave -p settings.php  


```
    access_log  /var/log/nginx/project.access.log requestTime;    
    error_log  /var/log/nginx/project.error.log;
    root /home/workdir/project/public;
```




## Generate environment settings



A file that returns an array of what env settings are required by this application

```
<?php

use ImagickDemo\Config;

$env = [
    Config::CACHING_SETTING,
    Config::SCRIPT_PACKING,
];

return $env;
```


Produces a file containing a single function that contains all the requested env settings.

```
<?php

function getAppEnv() {
    static $env = [
        'caching.setting' => 'caching.revalidate',
        'script.packing' => '',
    ];

    return $env;
}

```

The keys are the actual strings, rather than the constants used in the application, to allow the settings to be used outside of the application. 





## Convert PHP ini files to PHP-FPM format

Because of reasons, PHP-FPM doesn't use the standard PHP in file format when including ini files in a pool in the PHP-FPM conf file.

This aspect of the Configurator converts PHP style ini files:

```
extension=imagick.so
default_charset = "utf-8";
post_max_size = 10M
```

to PHP-FPM style files:

```
php_admin_value[extension] = "imagick.so"
php_admin_value[default_charset] = "utf-8"
php_admin_value[post_max_size] = "10M"
```
