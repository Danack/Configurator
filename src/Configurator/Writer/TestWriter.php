<?php


namespace Configurator\Writer;

use Configurator\Writer;
use Configurator\ConfiguratorException;

class TestWriter implements Writer
{
    private $fileData = [];

    /**
     * @param $input
     * @param $output
     * @throws \Exception
     */
    public function writeFile($filename, $data)
    {
        $this->fileData[$filename] = $data;
    }
    
    public function getDataForFile($filename)
    {
        if (array_key_exists($filename, $this->fileData) === false) {
            throw new ConfiguratorException("File [$filename] was not written");
        }

        return $this->fileData[$filename];
    }
}
