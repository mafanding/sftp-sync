<?php
namespace SftpSync;

use SftpSync\Interfaces\ShellInterface;
use SftpSync\Interfaces\CommandsInterface;
use SftpSync\Commands;

class Shell implements ShellInterface
{

    const SUCCESS = 0;

    protected $histroyCommands;

    protected $currentCommand;

    protected $allowCommands;

    public function __construct(CommandsInterface $commmands = null)
    {
        $this->allowCommands = $commmands ?? new Commands;
        $this->histroyCommands = [];
        $this->currentCommand = "";
    }

    public function run()
    {
        var_dump($this->currentCommand);
        exec($this->currentCommand, $output, $code);
        if ($code !== self::SUCCESS) {
            throw new Exception("Failed to exec command: {$this->currentCommand}; $output");
        }
        $this->histroyCommands[] = $this->currentCommand;
        $this->currentCommand = "";
    }

    public function __call($name, $arguments)
    {
        if (!$this->allowCommands->commandExists($name)) {
            throw new Exception("The shell does't support $name command");
        }
        $this->currentCommand = sprintf("%s %s %s", $this->currentCommand, $this->allowCommands->realCommand($name), $arguments[0] ?? "");
        return $this;
    }

}
