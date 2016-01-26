<?php


namespace Configurator\Writer;

use Configurator\Writer;
use Configurator\ConfiguratorException;

class FileWriter implements Writer
{
    /**
     * @param $input
     * @param $output
     * @throws \Exception
     */
    public function writeFile($filename, $data)
    {
        $written = @file_put_contents($filename, $data);
        if ($written === false) {
            throw new ConfiguratorException("Failed to write config to file [$filename]");
        }
    }
}
