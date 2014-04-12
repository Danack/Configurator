<?php

namespace Intahwebz\Configurator {


    class Functions {
        static function load(){}
    }
}

namespace {


function convertToFPM($inputFilename, $outputFilename) {

    $iniSettings = parse_ini_file($inputFilename);

    $filehandle = fopen($outputFilename, "w");

    if ($filehandle === false) {
        throw new \Exception("Could not open file $outputFilename for output.");
    }

    foreach ($iniSettings as $key => $value) {

        if (is_bool($value) == true ||
            $value === 0 ||
            $value === 1) {

            if ($value === false) {
                $value = "0";
            }

            fwrite($filehandle, "php_admin_flag[$key] = $value".PHP_EOL);
        }
        else {
            fwrite($filehandle, "php_admin_value[$key] = \"$value\"".PHP_EOL);
        }
    }

    fclose($filehandle);
}

}