<?php

use ExampleApp\AppConfig; 

//default settings get applied to all environments
$default = [];
$default[AppConfig::FILE_STORAGE] = 'file.storage.s3';

$live = [];
$live[AppConfig::SCRIPT_PACKING] = true;
$live[AppConfig::CACHING_SETTING] = 'caching.time';

$dev = [];
$dev[AppConfig::SCRIPT_PACKING] = false;
$dev[AppConfig::CACHING_SETTING] = 'caching.revalidate';
//Override the default for dev environments
$dev[AppConfig::FILE_STORAGE] = 'file.storage.localfilesystem';

// Anyone doing UX testing needs to have the scripts packed together
// to avoid slow UI responses
$uxtesting[AppConfig::SCRIPT_PACKING] = true;

// Sometimes you just want to force a setting to a particular value
// without figuring out where it is being set. Setting it in an 
// override setting will set it for all environments.
//$override[AppConfig::SCRIPT_PACKING] = false;