<?php
namespace SftpSync;

use SftpSync\Interfaces\ShellInterface;
use SftpSync\Interfaces\CommandsInterface;
use SftpSync\Commands;
use Exception;

class Shell implements ShellInterface
{

    const SUCCESS = 0;

    protected $histroyCommands;

    protected $currentCommand;

    protected $allowCommands;

    protected $code;

    protected $output;

    protected $flag;

    public function __construct(CommandsInterface $commmands = null)
    {
        $this->allowCommands = $commmands ?? new Commands;
        $this->histroyCommands = [];
        $this->currentCommand = "";
        $this->reset();
    }

    public function run()
    {
        $this->flag = 1;
        exec($this->currentCommand, $this->output, $this->code);
        if ($this->code !== self::SUCCESS) {
            throw new Exception("Failed to exec command [code={$this->code}]: {$this->currentCommand}; " . var_export($this->output, true));
        }
        $this->histroyCommands[] = $this->currentCommand;
        $this->currentCommand = "";
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (!$this->allowCommands->commandExists($name)) {
            throw new Exception("The shell does't support $name command");
        }
        if ($this->flag) {
            $this->reset();
        }
        $this->currentCommand = sprintf("%s %s %s", $this->currentCommand, $this->allowCommands->realCommand($name), $arguments[0] ?? "");
        return $this;
    }

    public function getOutput()
    {
        if(!$this->flag) {
            $this->run();
        }
        return $this->output;
    }

    public function getCode()
    {
        if(!$this->flag) {
            $this->run();
        }
        return $this->code;
    }

    protected function reset()
    {
        $this->output = "";
        $this->code = self::SUCCESS;
        $this->flag = 0;
    }

}
