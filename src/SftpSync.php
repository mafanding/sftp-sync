<?php
namespace SftpSync;

use SftpSync\Git\GitParse;
use Exception;

class SftpSync
{

    public static function run()
    {
        try {
            $configs = Configure::load(DEFAULT_CONFIG_FILE);
            Sftp::sync(GitParse::parse(), $configs);
        } catch (Exception $e) {
            printf("%s\n", $e->getMessage());
            exit(255);
        }
    }

}
