<?php
namespace SftpSync\Interfaces;

interface CommandsInterface
{

    public function commandExists($command);

    public function realCommand($command);
}
