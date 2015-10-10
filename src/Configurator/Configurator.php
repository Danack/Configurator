<?php

namespace Configurator;


class Configurator
{
    private $config = array();
    private $configDefault = array();
    private $configEnvironment = array();
    private $configOverride = array();
    
    /**
     * @var
     */
    private $inputFilename;

    /**
     * @var
     */
    private $outputFilename;

    private $environment;
    
    /**
     * @var
     */
    private $environmentList;

    public function __construct(
        $environment,
        $jssettings = '',
        $phpsettings = ''
    ) {
        if (count($jssettings) == 0 && count($phpsettings) == 0) {
            throw new ConfiguratorException("One of PHP setting or JS settings must be set.");
        }
        
        $this->environment = $environment;
        $this->environmentList = explode(',', $environment);

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
                $this->addJSConfig($jsSetting);
            }
        }

    }

    public function writeConfigFile($input, $output)
    {
        $outputFilename = $output;
        $inputFilename = $input;
        
        $config = $this->configurate($inputFilename);
        $written = @file_put_contents($outputFilename, $config);
        if (!$written) {
            throw new \Exception("Failed to write config to file `$this->outputFilename`");
        }
    }

    
    public function writeEnvironmentFile($input, $output, $namespace = false)
    {
        $inputFilename = $input;
        $outputFilename = $output;
        
        $output = $this->genEnvironmentFile($inputFilename, $namespace);
        $written = @file_put_contents($outputFilename, $output);
        if (!$written) {
            throw new \Exception("Failed to write config to file `$this->outputFilename`");
        }
    }
    

    /**
     * @param $environment
     * @param $filename
     */
    function addJSConfig($filename)
    {
        $contents = @file_get_contents($filename);
        if ($contents == false) {
            throw new ConfiguratorException("Could not read file $filename.");
        }

        $data = json_decode($contents, true);
        
        if ($data === false) {
            throw new ConfiguratorException("Could not json_decode file $filename.");
        }

        if (array_key_exists('default', $data) == true) {
            $this->addConfigDefault($data['default']);
        }
        
        foreach ($this->environmentList as $environment) {
            if (array_key_exists($environment, $data) == true) {
                $this->addConfigEnvironment($data[$environment]);
            }
        }

        if (array_key_exists('override', $data) == true) {
            $this->addConfigOverride($data['override']);
        }
    }

    private function addConfigDefault($data) {
        $this->configDefault = array_merge($this->configDefault, $data);
    }

    private function addConfigEnvironment($data) {
        $this->configEnvironment = array_merge($this->configEnvironment, $data);
    }

    private function addConfigOverride($data) {
        $this->configOverride = array_merge($this->configOverride, $data);
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
        
        if (strlen($filename) == 0) {
            throw new ConfiguratorException("Zero length filename is bogus.");
        }
        
        if (file_exists($filename) == false) {
            throw new ConfiguratorException("File `$filename` does not exist.");
        }
        
        ob_start();
        require($filename);
        $contents = ob_get_contents();
        ob_end_clean();

        if (strlen($contents) != 0) {
            $message = sprintf(
                "Filename `%s` output some characters. Please check it is a valid PHP file.\n",
                $filename
            );

            throw new ConfiguratorException($message);
        }

        if (isset($default) == true) {
            $this->addConfigDefault($default);
        }

        foreach ($this->environmentList as $environment) {
            if (isset($$environment) == true) {
                $this->addConfigEnvironment($$environment);
            }
        }

        if (isset($override) == true) {
            $this->addConfigOverride($override);
        }

        if (isset($evaluate) == true) {
            $calculatedValues = $evaluate($this->getConfig(), $this->environment);
            $this->addConfigOverride($calculatedValues);
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
    public function addConstant($constantName) {
        if (defined($constantName) == false) {
            throw new ConfiguratorException("Constant [$constantName] is not available, cannot configurate.");
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
    public function configurate($inputFilename) {
        $config = $this->getConfig();
        
        foreach($config as $key => $value) {
            if ($value === false) {
                $$key = 'false';
                continue;
            }
            
            $$key = $value;
        }

        $configuration = require $inputFilename;
        
        return $configuration;
    }
    
    
    private function genEnvironmentFile($input, $namespace)
    { 
        $config = $this->getConfig();
        
        $inputFilename = $input;
        
        $envRequired = require $inputFilename;
        
        if (is_array($envRequired) == false) {
            throw new ConfiguratorException("Failed to get array from ".$inputFilename);
        }
        
        $envOutput = "<?php\n";
        $envOutput .= "\n";
        
        if (strlen($namespace)) {
            $envOutput .= "namespace $namespace;\n";
            $envOutput .= "\n";
        }
        
        
        $envOutput .= "function getAppEnv() {\n";
        $envOutput .= "    static \$env = [\n"; 
        
        foreach ($envRequired as $env) {
            if (array_key_exists($env, $config) == false) {
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

    public function getConfig()
    {
        $config = $this->config;
        $config = array_merge($config, $this->configDefault);
        $config = array_merge($config, $this->configEnvironment);
        $config = array_merge($config, $this->configOverride);
        
        return $config;
    }
}

