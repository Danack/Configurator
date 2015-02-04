<?php

namespace Configurator;

use Danack\Console\Output\BufferedOutput;
use Danack\Console\Formatter\OutputFormatterStyle;
use Danack\Console\Helper\QuestionHelper;
use Danack\Console\Command\Command;
use Danack\Console\Input\InputArgument;
use Auryn\Provider;



class CliRunner {

    const FPMCONV = 'fpmconv';
    const CONFIGURATE = 'configurate';
    
    private $defaultCommand;

    /**
     * @var \Configurator\ConfigurateApplication
     */
    private $console;
    
    function __construct($defaultCommand) {        
        if ($defaultCommand == CliRunner::FPMCONV) {
            $command = $this->makeFpmConvCommand();
        }
        else if ($defaultCommand == CliRunner::CONFIGURATE) {
            $command = $this->makeConfigurateCommand();
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
    function makeFpmConvCommand() {
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
    function makeConfigurateCommand() {
        $configurateCommand = new Command(
            self::CONFIGURATE,
            ['Configurator\Configurator', 'run']
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
            'The input filename'
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
        
        return $configurateCommand;
    }

    /**
     * 
     */
    function execute() {
        //Figure out what Command was requested.
        try {
            $parsedCommand = $this->console->parseCommandLine();
        }
        catch(\Exception $e) {
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
            $input = $parsedCommand->getInput();
        
            $injector = createInjector();
            $params = formatKeyNames($parsedCommand->getParams());
            $injector->execute($parsedCommand->getCallable(), $params);
        }
        catch (ConfiguratorException $sce) {
            echo "Error running task: \n";
            echo $sce->getMessage();
            exit(-1);
        }
        catch (\Exception $e) {
            echo "Unexpected exception of type ".get_class($e)." running configurator`: ".$e->getMessage().PHP_EOL;
            echo $e->getTraceAsString();
            exit(-2);
        }
    }
}



function convertToFPM($inputFilename, $outputFilename) {
    $iniSettings = parse_ini_file($inputFilename);

    $fileHandle = fopen($outputFilename, "w");

    if ($fileHandle === false) {
        throw new \Exception("Could not open file $outputFilename for output.");
    }

    foreach ($iniSettings as $key => $value) {
        if (is_bool($value) == true ||
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

/**
 * @return Provider
 */
function createInjector() {
    $injector = new Provider();

    return $injector;
}


function formatKeyNames($params) {
    $newParams = [];
    foreach ($params as $key => $value) {
        $newParams[':'.$key] = $value;
    }

    return $newParams;
}