<?php

namespace Intahwebz\Configurator;

class Configurator {

	private $config = array();

	public function __construct(){
	}

	/**
	 * Adds an ini file to the configurator. All of the options are then available for
	 * generating config files from.
	 * @param $environment
	 * @param $filename
	 */
	public function	addConfig($environment, $filename){
		$config = parse_ini_file($filename, true);

		foreach($config[$environment] as $key => $value){
			$this->config[$key] = $value;
		}
	}

	/**
	 * Adds a constant with it's current value. This is useful for parsing the same settings
	 * used on the current machine to a new machine e.g. when machine A is being used as the puppet
	 * master for machine B, and you want machine B to use the same API key as machine A.
	 *
	 * @param $constantName
	 * @throws \Exception
	 */
	public function addConstant($constantName){
		if (defined($constantName) == false) {
			throw new \Exception("Constant [$constantName] is not available, cannot configurate.");
		}

		$this->config[$constantName] = constant($constantName);
	}

	/**
	 * Adds a name value pair to the config.
	 * @param $name
	 * @param $value
	 */
	public function addConfigValue($name, $value){
		$this->config[$name] = $value;
	}

	/**
	 * Reads a config file applies the current config settings to it and returns
	 * the generated result.
	 *
	 * @param $inputFilename
	 * @return mixed
	 */
	public function configurate($inputFilename){
		foreach($this->config as $key => $value){
			$$key = $value;
		}

		$configuration = require $inputFilename;
		return $configuration;
	}
}

