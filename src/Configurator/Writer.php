<?php


namespace Configurator;

interface Writer
{
    public function writeFile($filename, $data);
}
