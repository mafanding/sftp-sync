<?php
namespace SftpSync;

use SftpSync\Git\GitParse;
use Exception;

class SftpSync
{

    public static function run()
    {
        try {
            $list = GitParse::parse();
            var_dump($list->getAll());
        } catch (Exception $e) {
            printf("%s\n", $e->getMessage());
            exit(255);
        }
    }

}
