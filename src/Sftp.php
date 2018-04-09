<?php
namespace SftpSync;

use SftpSync\Interfaces\ListInterface;
use SftpSync\Interfaces\ShellInterface;
use SftpSync\Interfaces\ConfigureInterface;
use Exception;

class Sftp
{

    public static function sync(ListInterface $list, ConfigureInterface $config, ShellInterface $shell = null)
    {
        $files = $list->getAll();
        if (empty($files)) {
            return true;
        }
        if (is_null($shell)) {
            $shell = new Shell();
        }
        $re = static::composeBatchFile($files, $config);
        if ($re === false) {
            throw new Exception("Failed to create batch file");
        }
        try {
            $shell->sftp(sprintf("-P %s -b %s %s@%s", $config->remotePort, $config->localDocumentRoot . $config->batchFile, $config->remoteUser, $config->remoteIp))->run();
        } catch (Exception $e) {
            @unlink($config->localDocumentRoot . $config->batchFile);
            throw $e;
        } finally {
            @unlink($config->localDocumentRoot . $config->batchFile);
        }
    }

    protected static function composeBatchFile($files, $config)
    {
        $content = [];
        $content[] = sprintf("cd %s", $config->remoteDocumentRoot);
        foreach ($files as $v) {
            $content[] = sprintf("put %s %s", $config->localDocumentRoot . $v, $v);
        }
        return file_put_contents($config->localDocumentRoot . $config->batchFile, implode(PHP_EOL, $content));
    }

}
