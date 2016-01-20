<?php

namespace ExampleApp;

/**
 * Class AppConfig
 * 
 * An example class to show how config variables can be referenced in config
 * files and in actual code programmatically with shared names.
 */
class AppConfig
{
    // Whether assets should be cached or not.
    const CACHING_SETTING = 'caching.setting';
    
    // Whether JS / CSS script are packed together
    // or served separately.
    const SCRIPT_PACKING = 'script.packing';
    
    // What type of file storage this system should be using.
    const FILE_STORAGE = 'file.storage';
}
