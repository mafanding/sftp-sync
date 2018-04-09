<?php
namespace SftpSync\Git;

use SftpSync\Interfaces\ParseInterface;
use SftpSync\Interfaces\ShellInterface;
use SftpSync\Shell;
use Exception;

class GitParse implements ParseInterface
{

    public static function parse(ShellInterface $shell = null)
    {
        if (!file_exists(".git")) {
            throw new Exception("Failed to parse this directory with git: .git doesn't exists");
        }
        if (is_null($shell)) {
            $shell = new Shell();
        }
        return static::getList($shell);
    }

    protected static function getList(ShellInterface $shell)
    {
        $list = new GitList;
        $output = $shell->git("status --porcelain")->pipe()->wc("-l")->getOutput();
        $count = isset($output[0]) ? intval($output[0]) : 0;
        if ($count <= 0) {
            return $list;
        }
        $list->parseList($shell->git("status --porcelain")->getOutput());
        return $list;
    }

}
