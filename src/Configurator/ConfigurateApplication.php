<?php

namespace Configurator;

use Danack\Console\Application as ConsoleApplication;
use Danack\Console\Input\InputInterface;

class ConfigurateApplication extends ConsoleApplication
{
    private $defaultCommand;

    public function __construct($defaultCommand, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        $this->defaultCommand = $defaultCommand;
        parent::__construct($name, $version);
    }

    /**
     * Get the command to run
     * @param InputInterface $input
     * @return string
     */
    protected function getCommandName(InputInterface $input)
    {
        // This should return the name of your command.
        return $this->defaultCommand;
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        return $defaultCommands;
    }

    /**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
