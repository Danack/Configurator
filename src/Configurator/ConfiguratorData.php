<?php


namespace Configurator;

class ConfiguratorData
{
    private $environmentList;
    
    private $config = array();
    private $configDefault = array();
    private $configEnvironment = array();
    private $configOverride = array();
    
    
    public function __construct($environmentList)
    {
        $this->environmentList = $environmentList;
    }
    
    public function addData(array $data)
    {
        if (array_key_exists('default', $data) === true) {
            $this->addConfigDefault($data['default']);
        }

        foreach ($this->environmentList as $environment) {
            if (array_key_exists($environment, $data) === true) {
                $this->addConfigEnvironment($data[$environment]);
            }
        }

        if (array_key_exists('override', $data) === true) {
            $this->addConfigOverride($data['override']);
        }
    }
    
    public function addConfigDefault($data)
    {
        $this->configDefault = array_merge($this->configDefault, $data);
    }

    public function addConfigEnvironment($data)
    {
        $this->configEnvironment = array_merge($this->configEnvironment, $data);
    }

    public function addConfigOverride($data)
    {
        $this->configOverride = array_merge($this->configOverride, $data);
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
        if (defined($constantName) === false) {
            throw new ConfiguratorException("Constant [$constantName] is not available, cannot configurate.");
        }

        $this->config[$constantName] = constant($constantName);
    }

    /**
     * Adds a name value pair to the config.
     * @param $name
     * @param $value
     */
    public function addConfigValue($name, $value)
    {
        $this->config[$name] = $value;
    }
    
        /**
     * @return array
     */
    public function getConfig()
    {
        $config = $this->config;
        $config = array_merge($config, $this->configDefault);
        $config = array_merge($config, $this->configEnvironment);
        $config = array_merge($config, $this->configOverride);
        
        return $config;
    }
}
