<?php
namespace SftpSync;

use SftpSync\Git\GitParse;
use SftpSync\Sftp\Sftp;
use SftpSync\Configure\Configure;
use SftpSync\Git\GitCommit;
use Exception;

class SftpSync
{

    public static function run()
    {
        try {
            $opts = static::getOpt();
            if (isset($opts["h"]) || isset($opts["help"])) {
                static::printHelp();
            } elseif (isset($opts["v"]) || isset($opts["version"])) {
                static::printVersion();
            }
            $configFile = realpath($opts["conf"] ?? ($opts["c"] ?? null));
            $configs = Configure::load($configFile ? $configFile : DEFAULT_CONFIG_FILE);
            $list = GitParse::parse();
            Sftp::sync($list, $configs);
            if (($configs->autoCommit || isset($opts["a"]) || isset($opts["auto-commit"])) && !empty($list->getAll())) {
                $gitCommit = new GitCommit($list, $configs);
                $gitCommit->run();
            }
        } catch (Exception $e) {
            printf("%s\n", $e->getMessage());
            exit(255);
        }
    }

    protected static function getOpt()
    {
        $shortOpt = "m:hc:va";
        $longOpt = [
            "message:",
            "help",
            "conf:",
            "version",
            "auto-commit",
        ];
        return getopt($shortOpt, $longOpt);
    }

    protected static function printHelp()
    {
        echo <<<EOF

Usage:
    command [options]

Options:
    -h, --help                  Display this message
    -v, --version               Display this application version
    -m, --message=[MESSAGE]     Commit message
    -a, --auto-commit           The modified files will be auto committed
    -c, --conf=[CONFIG]         If specified, use the given file as config file

EOF;
        exit(0);
    }

    protected static function printVersion()
    {
        printf("Sftp-sync tool for linux. Version: %s\n", SFTP_SYNC_VERSION);
        exit(0);
    }

}
