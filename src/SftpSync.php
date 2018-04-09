<?php
namespace SftpSync;

use SftpSync\Git\GitParse;
use Exception;

class SftpSync
{

    public static function run()
    {
        try {
            $configs = Configure::load(getcwd() . DIRECTORY_SEPARATOR . "sftp-sync.json");
            $list = GitParse::parse();
            Sftp::sync($list, $configs);
        } catch (Exception $e) {
            printf("%s\n", $e->getMessage());
            exit(255);
        }
    }

}
