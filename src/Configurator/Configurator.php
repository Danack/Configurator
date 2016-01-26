<?php

namespace Configurator;

use Configurator\Writer;
use Symfony\Component\Yaml\Yaml;

class Configurator
{
    /** @var string The comma separated list of environments that
     * the config should be generated for.
     */
    private $environment;
    
    /**
     * @var string[] The individual environment components.
     */
    private $environmentList;

    /** @var ConfiguratorData  */
    private $configuratorData;
    
    /** @var string[] The original arguments passed to the converter */
    private $originalArgs;

    /**
     * @param Writer $writer
     * @param $environment
     * @param array $originalArgs
     * @param string $jssettings
     * @param string $phpsettings
     * @param string $yamlsettings
     * @throws ConfiguratorException
     */
    public function __construct(
        Writer $writer,
        $environment,
        array $originalArgs,
        $jssettings = '',
        $phpsettings = '',
        $yamlsettings = ''
    ) {
        $totalInputFilesLength = strlen($jssettings) + strlen($phpsettings) + strlen($yamlsettings);
        
        if ($totalInputFilesLength === 0) {
            throw new ConfiguratorException("One of PHP, YAML or JS settings files must be set.");
        }

        $this->writer = $writer;
        $this->environment = $environment;
        $this->originalArgs = $originalArgs;
        
        $this->environmentList = explode(',', $environment);
        $this->configuratorData = new ConfiguratorData($this->environmentList);

        $phpsettings = trim($phpsettings);
        if (strlen($phpsettings) > 0) {
            $phpsettingArray = explode(',', $phpsettings);
            foreach ($phpsettingArray as $phpSetting) {
                $this->addPHPConfig($phpSetting);
            }
        }

        $jssettings = trim($jssettings);
        if (strlen($jssettings) > 0) {
            $jsSettingsArray = explode(',', $jssettings);
            foreach ($jsSettingsArray as $jsSetting) {
                $this->addJSONConfig($jsSetting);
            }
        }
        
        $yamlsettings = trim($yamlsettings);
        if (strlen($yamlsettings) > 0) {
            $yamlSettingsArray = explode(',', $yamlsettings);
            foreach ($yamlSettingsArray as $yamlSetting) {
                $this->addYamlConfig($yamlSetting);
            }
        }
    }

    /**
     * @param $input
     * @param $output
     * @throws \Exception
     */
    public function writeConfigFile($input, $output)
    {
        $outputFilename = $output;
        $inputFilename = $input;
        $config = $this->configurate($inputFilename);
        $this->writer->writeFile($outputFilename, $config);
    }

    /**
     * @param $input
     * @param $output
     * @param bool $namespace
     * @throws ConfiguratorException
     * @throws \Exception
     */
    public function writeEnvironmentFile($input, $output, $namespace = false)
    {
        $inputFilename = $input;
        $outputFilename = $output;
        
        $output = $this->genEnvironmentFile($inputFilename, $namespace);
        $this->writer->writeFile($outputFilename, $output);
    }

    /**
     * @param $environment
     * @param $filename
     */
    public function addJSONConfig($filename)
    {
        $contents = @file_get_contents($filename);
        if ($contents === false) {
            throw new ConfiguratorException("Could not read file $filename.");
        }

        $data = json_decode($contents, true);

        if ($data === false) {
            throw new ConfiguratorException("Could not json_decode file $filename.");
        }
        
        $this->configuratorData->addData($data);
    }


    /**
     * @param $filename
     * @throws ConfiguratorException
     */
    public function addYamlConfig($filename)
    {
        $contents = @file_get_contents($filename);
        if ($contents === false) {
            throw new ConfiguratorException("Could not read file $filename.");
        }

        $data = Yaml::parse($contents, true);
        
        $this->configuratorData->addData($data);
    }
    
    /**
     * Adds an ini file to the configurator. All of the options are then available for
     * generating config files from.
     * @param $environment
     * @param $filename
     */
    public function addPHPConfig($filename)
    {
        $filename = trim($filename);
        
        if (strlen($filename) === 0) {
            throw new ConfiguratorException("Zero length filename is bogus.");
        }
        
        if (file_exists($filename) === false) {
            throw new ConfiguratorException("File `$filename` does not exist.");
        }
        
        ob_start();
        require($filename);
        $contents = ob_get_contents();
        ob_end_clean();

        if (strlen($contents) !== 0) {
            $message = sprintf(
                "Filename `%s` output some characters. Please check it is a valid PHP file.\n",
                $filename
            );

            throw new ConfiguratorException($message);
        }

        if (isset($default) === true) {
            $this->configuratorData->addConfigDefault($default);
        }

        foreach ($this->environmentList as $environment) {
            if (isset($$environment) === true) {
                $this->configuratorData->addConfigEnvironment($$environment);
            }
        }

        if (isset($override) === true) {
            $this->configuratorData->addConfigOverride($override);
        }

        if (isset($evaluate) === true) {
            $calculatedValues = $evaluate($this->configuratorData->getConfig(), $this->environment);
            $this->configuratorData->addConfigOverride($calculatedValues);
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
    public function addConstant($constantName)
    {
        $this->configuratorData->addConstant($constantName);
    }

    /**
     * Adds a name value pair to the config.
     * @param $name
     * @param $value
     */
    public function addConfigValue($name, $value)
    {
        $this->configuratorData->addConfigValue($name, $value);
    }

    /**
     * Reads a config file applies the current config settings to it and returns
     * the generated result.
     *
     * @param $inputFilename
     * @return mixed
     */
    public function configurate($inputFilename)
    {
        $config = $this->configuratorData->getConfig();
        
        foreach ($config as $key => $value) {
            if ($value === false) {
                $$key = 'false';
                continue;
            }
            
            $$key = $value;
        }

        $configuration = require $inputFilename;
        
        return $configuration;
    }

    /**
     * @param $input
     * @param $namespace
     * @return string
     * @throws ConfiguratorException
     */
    private function genEnvironmentFile($input, $namespace)
    {
        $config = $this->configuratorData->getConfig();
        
        $inputFilename = $input;
        
        $envRequired = require $inputFilename;
        
        if (is_array($envRequired) === false) {
            throw new ConfiguratorException("Failed to get array from ".$inputFilename);
        }
        
        $envOutput = "<?php\n";
        $envOutput .= "\n";

        
        $envOutput .= "// This file was automatically generated with the command line:\n";
        $envOutput .= sprintf(
            "// %s \n",
            str_replace('?>', '', implode(' ', $this->originalArgs))
        );

        $envOutput .= "\n";
        
        if (strlen($namespace) !== 0) {
            $envOutput .= "namespace $namespace;\n";
            $envOutput .= "\n";
        }

        $envOutput .= "function getAppEnv() {\n";
        $envOutput .= "    static \$env = [\n";
        
        foreach ($envRequired as $env) {
            if (array_key_exists($env, $config) === false) {
                throw new ConfiguratorException("App needs $env but not found in config");
            }
            $value = $config[$env];
            $valueText = var_export($value, true);

            $envOutput .= sprintf(
                "        '%s' => %s,\n",
                $env,
                $valueText
            );
        }
        $envOutput .= "    ];\n";
        $envOutput .= "\n";
        $envOutput .= "    return \$env;\n";
        $envOutput .= "}\n";

        return $envOutput;
    }
}
