<?php
namespace SftpSync;

use SftpSync\Interfaces\CommandsInterface;

class Commands implements CommandsInterface
{

    protected $commandTable;

    public function __construct($extraCommands = [])
    {
        $this->commandTable = array_merge([
            "which" => "which",
            "git" => "git",
            "sftp" => "sftp",
            "wc" => "wc",
            "pipe" => "|",
            "grep" => "grep",
        ], $extraCommands);
    }

    public function commandExists($command)
    {
        return array_key_exists($command, $this->commandTable);
    }

    public function realCommand($command)
    {
        return $this->commandTable[$command] ?? $command;
    }

}
