<?php

use ExampleApp\AppConfig;

// This is the list of environment variables that MUST be set.
// This list is checked as the last step of the genenv command, to
// ensure that required variables were set by the env choices made.
$env = [
    AppConfig::CACHING_SETTING,
    AppConfig::SCRIPT_PACKING,
    AppConfig::FILE_STORAGE
];

return $env;
