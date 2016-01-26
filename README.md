Configurator
============

A few simple tools to manage configuration data sanely. These tools are to hold environment data that can be shared without risk. They are not designed to hold 'secrets' e.g. api/oauth keys.

* Config file generator

* Environment settings generator

* Convert PHP ini files to PHP-FPM format

Philosophy
----------

Environment variables need to be granular controls. Although they can be grouped together as "environments", they need to be configurable on a per-deploy basis without duplicating large blocks of information. 
 
They also need to be stored stored alongside the applications code so that they can be maintained easily. 

This library allows you to do these two thing. All environment settings can be stored in a simple way, and then extracted and combined with arbitrary combinations. e.g. using 'centos,dev' as the environment setting uses all the 'centos' environment settings, with the 'dev' settings over-riding any duplicate settings.


Example usage for people who don't like reading instructions
------------------------------------------------------------

If you install Configurator through Composer, the executable files will be in the vendor bin directory and can be run with:

```
#Generate nginx config file for the centos,dev environment
vendor/bin/configurate -p example/config.php example/config_template/nginx.conf.php autogen/nginx.conf "centos,dev"

# Generate a PHP file that contains a function that return the current application env settings
vendor/bin/genenv -p example/config.php example/envRequired.php autogen/appEnv.php "centos,dev"

# Convert a PHP ini file to be in the PHP-FPM format
vendor/bin/fpmconv autogen/php.ini autogen/php.fpm.ini
```




Config file generator
---------------------

This tool allows you to generate config files from PHP based templates and PHP data files that hold all of the setting for the different environments

Source config template file:

```
<?php

$config = <<< END

server {

    access_log  ${'nginx.log.directory'}/project.access.log requestTime;    
    error_log  ${'nginx.log.directory'}/project.error.log;
    root ${'project.root.directory'}/public;
}

END;

return $config;

```

Data file that holds data for arbitrary environments:

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

$john = [
    'project.root.directory' => '/home/workdir/project',
]

```

Running the command `configurate data/nginx.conf.php var/generated/nginx.conf centos,john -p settings.php` would generated the file:  


```
    access_log  /var/log/nginx/project.access.log requestTime;    
    error_log  /var/log/nginx/project.error.log;
    root /home/workdir/project/public;
```


### Syntax

`configurate [-p|--phpsettings="..."] [-j|--jssettings="..."] input output environment`

-p - a comma separated list of PHP data files. Each need to return an array of data.
-j - a comma separated list of JSON data files.
-y - a comma separated list of YAML files.
input - the input template file.  
output - the output file to write.  
environment - a comma separated list of environment settings to apply.  



Generate environment settings
-----------------------------

A tool that will parse the environment settings required by an application, and the data files that hold the settings for all environments, and will generated a file that contains a function that returns an array of what env settings are required by this application


File listing the environment settings required by the application:

```
<?php

use ExampleApp\AppConfig;

$env = [
    AppConfig::CACHING_SETTING,
    AppConfig::SCRIPT_PACKING,
    AppConfig::FILE_STORAGE
];

return $env;
```

A data file that holds all the data for the various environment settings

```
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
```

Running the command `bin/genenv -p environment/config.php environment/envRequired.php env.php dev,uxtesting`

Produces a file containing a single function that contains all the requested env settings.

```
<?php

function getAppEnv() {
    static $env = [
        'caching.setting' => 'caching.revalidate',
        'script.packing' => 'true',
    ];

    return $env;
}

```

The keys are the actual strings, rather than the constants used in the application, to allow the settings to be used outside of the application. 


### Syntax

`genenv [-p|--phpsettings="..."] [-j|--jssettings="..."] input output environment`

-p - a comma separated list of PHP data files. Each need to return an array of data.  
-j - a comma separated list of JSON data files.
-y - a comma separated list of YAML files.
input - the input template file.  
output - the output file to write.  
environment - a comma separated list of environment settings to apply.  

Convert PHP ini files to PHP-FPM format
---------------------------------------

Because of reasons, PHP-FPM doesn't use the standard PHP in file format when including ini files in a pool in the PHP-FPM conf file. This aspect of the Configurator converts PHP style ini files to the format PHP-FPM expects:

Input ini file:

```
extension=imagick.so
default_charset = "utf-8";
post_max_size = 10M
```

Running the command `php bin/fpmconv example.php.ini example.phpfpm.ini`

will generate this PHP-FPM ini file

```
php_admin_value[extension] = "imagick.so"
php_admin_value[default_charset] = "utf-8"
php_admin_value[post_max_size] = "10M"
```
