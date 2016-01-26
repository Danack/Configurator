<?php

namespace Configurator;

use Danack\Console\Output\BufferedOutput;
use Danack\Console\Formatter\OutputFormatterStyle;
use Danack\Console\Helper\QuestionHelper;
use Danack\Console\Command\Command;
use Danack\Console\Input\InputArgument;
use Auryn\Injector;
use Configurator\ConfiguratorException;

/**
 *
 */
function errorHandler($errno, $errstr, $errfile, $errline)
{
    if (error_reporting() === 0) {
        return true;
    }

    if ($errno === E_DEPRECATED) {
        return true; //Don't care - deprecated warnings are generally not useful
    }
    
    if ($errno === E_CORE_ERROR || $errno === E_ERROR) {
        // PHP will shut down anyway.
        return false;
    }

    $message = "Error: [$errno] $errstr in file $errfile on line $errline<br />\n";
    throw new \Exception($message);
}

set_error_handler('Configurator\errorHandler');

class CliRunner
{
    const GENENV = 'genenv';
    const FPMCONV = 'fpmconv';
    const CONFIGURATE = 'configurate';
    
    private $defaultCommand;

    private $originalArgs;
    
    /**
     * @var \Configurator\ConfigurateApplication
     */
    private $console;
    
    public function __construct($defaultCommand, $originalArgs)
    {
        $this->originalArgs = $originalArgs;
        
        if ($defaultCommand === CliRunner::FPMCONV) {
            $command = $this->makeFpmConvCommand();
        }
        else if ($defaultCommand === CliRunner::CONFIGURATE) {
            $command = $this->makeConfigurateCommand();
        }
        else if ($defaultCommand === CliRunner::GENENV) {
            $command = $this->makeGenerateEnvCommand();
        }
        else {
            throw new \Exception("Unknown command type");
        }

        $this->console = new ConfigurateApplication($defaultCommand, "Configurator", "1.0.0");
        $this->console->add($command);
    }

    /**
     * @return Command
     */
    public function makeFpmConvCommand()
    {
        $convertCommand = new Command(
            self::FPMCONV,
            'Configurator\convertToFPM'
        );
        $convertCommand->addArgument(
            'inputFilename',
            InputArgument::REQUIRED,
            'The input filename'
        );
        $convertCommand->addArgument(
            'outputFilename',
            InputArgument::REQUIRED,
            'The input filename'
        );

        return $convertCommand;
    }

    /**
     * @return Command
     */
    public function makeConfigurateCommand()
    {
        $configurateCommand = new Command(
            self::CONFIGURATE,
            ['Configurator\Configurator', 'writeConfigFile']
        );
        $configurateCommand->setDescription("Transform a config file into an output file");

        $configurateCommand->addArgument(
            'input',
            InputArgument::REQUIRED,
            'The input filename'
        );

        $configurateCommand->addArgument(
            'output',
            InputArgument::REQUIRED,
            'The output filename'
        );

        $configurateCommand->addArgument(
            'environment',
            InputArgument::REQUIRED,
            'What environment to generated the config for.'
        );

        $configurateCommand->addOption(
            'phpsettings',
            'p',
            InputArgument::OPTIONAL,
            'A comma separated list of PHP setting files.'
        );

        $configurateCommand->addOption(
            'jssettings',
            'j',
            InputArgument::OPTIONAL,
            'A comma separated list of JSON setting files.'
        );
        
        $configurateCommand->addOption(
            'yamlsettings',
            'y',
            InputArgument::OPTIONAL,
            'A comma separated list of YAML setting files.'
        );
        
        return $configurateCommand;
    }

    public function makeGenerateEnvCommand()
    {
        $configurateCommand = new Command(
            self::GENENV,
            ['Configurator\Configurator', 'writeEnvironmentFile']
        );

        $configurateCommand->setDescription("Generate a list of env settings into a PHP file");

        $configurateCommand->addArgument(
            'input',
            InputArgument::REQUIRED,
            'The input filename'
        );

        $configurateCommand->addArgument(
            'output',
            InputArgument::REQUIRED,
            'The output filename'
        );

        $configurateCommand->addArgument(
            'environment',
            InputArgument::REQUIRED,
            'What environment to generated the config for.'
        );

        $configurateCommand->addOption(
            'phpsettings',
            'p',
            InputArgument::OPTIONAL,
            'A comma separated list of PHP setting files.'
        );

        $configurateCommand->addOption(
            'jssettings',
            'j',
            InputArgument::OPTIONAL,
            'A comma separated list of JSON setting files.'
        );
        
        $configurateCommand->addOption(
            'namespace',
            'ns',
            InputArgument::OPTIONAL,
            'An optional namespace for the env function.'
        );

        return $configurateCommand;
    }

    /**
     *
     */
    public function execute()
    {
        //Figure out what Command was requested.
        try {
            $parsedCommand = $this->console->parseCommandLine();
        }
        catch (ConfiguratorException $ce) {
            echo "Problem running configuration: ".$ce->getMessage();
            exit(-1);
        }
        catch (\Exception $e) {
            //@TODO change to just catch parseException when that's implemented
            $output = new BufferedOutput();
            $this->console->renderException($e, $output);
            echo $output->fetch();
            exit(-1);
        }

        //Run the command requested, or the help callable if no command was input
        try {
            $output = $parsedCommand->getOutput();
            $formatter = $output->getFormatter();
            $formatter->setStyle('question', new OutputFormatterStyle('blue'));
            $formatter->setStyle('info', new OutputFormatterStyle('blue'));
        
            $questionHelper = new QuestionHelper();
            $questionHelper->setHelperSet($this->console->getHelperSet());

            // We currently have no config, so fine to create this directly.
            $injector = new Injector;

            $injector->alias('Configurator\Writer', 'Configurator\Writer\FileWriter');
            $injector->defineParam('originalArgs', $this->originalArgs);

            foreach ($parsedCommand->getParams() as $key => $value) {
                $injector->defineParam($key, $value);
            }
            
            $injector->execute($parsedCommand->getCallable());
        }
        catch (ConfiguratorException $ce) {
            echo "Error running task: \n";
            echo $ce->getMessage();
            exit(-1);
        }
        catch (\Exception $e) {
            echo "Unexpected exception of type ".get_class($e)." running configurator`: ".$e->getMessage().PHP_EOL;
            echo $e->getTraceAsString();
            exit(-2);
        }
    }
}

/**
 * Read an PHP ini file convert it to PHP-FPM format
 * and save it.
 *
 * @param $inputFilename
 * @param $outputFilename
 * @throws \Exception
 */
function convertToFPM($inputFilename, $outputFilename)
{
    $iniSettings = parse_ini_file($inputFilename);

    $fileHandle = fopen($outputFilename, "w");

    if ($fileHandle === false) {
        throw new \Exception("Could not open file $outputFilename for output.");
    }

    foreach ($iniSettings as $key => $value) {
        if (is_bool($value) === true ||
            $value === 0 ||
            $value === 1) {

            if ($value === false) {
                $value = "0";
            }

            fwrite($fileHandle, "php_admin_flag[$key] = $value".PHP_EOL);
        }
        else {
            fwrite($fileHandle, "php_admin_value[$key] = \"$value\"".PHP_EOL);
        }
    }

    fclose($fileHandle);
}
