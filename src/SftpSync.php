<?php
namespace SftpSync;

use SftpSync\Shell;

class SftpSync
{

    public static function run()
    {
        (new \SftpSync\Shell)->git("log")->pipe()->grep("php")->pipe()->wc("-l")->run();
    }

}
