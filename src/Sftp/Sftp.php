<?php
namespace SftpSync\Sftp;

use SftpSync\Interfaces\ListInterface;
use SftpSync\Interfaces\ShellInterface;
use SftpSync\Interfaces\ConfigureInterface;
use SftpSync\Shell;
use Exception;

class Sftp
{

    public static function sync(ListInterface $list, ConfigureInterface $config, ShellInterface $shell = null)
    {
        if (empty($list->getAll())) {
            return true;
        }
        if (is_null($shell)) {
            $shell = new Shell();
        }
        $re = static::composeBatchFile($list, $config);
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
        return true;
    }

    protected static function composeBatchFile($list, $config)
    {
        $content = [];
        $content[] = sprintf("cd %s", $config->remoteDocumentRoot);
        $content = static::appendBatchContent($list->getModified(), $content, $config, "M");
        $content = static::appendBatchContent($list->getNews(), $content, $config, "A");
        $content = static::appendBatchContent($list->getUntracked(), $content, $config, "?");
        $content = static::appendBatchContent($list->getDeleted(), $content, $config, "D");
        return file_put_contents($config->localDocumentRoot . $config->batchFile, implode(PHP_EOL, $content));
    }

    protected static function appendBatchContent($files, $content, $config, $action = "A")
    {
        if (empty($files)) {
            return $content;
        }
        foreach ($files as $v) {
            if (!in_array($v, $config->syncExcludes)) {
                switch ($action) {
                    case "A":
                    case "M":
                    case "?":
                        $content[] = sprintf("put %s %s", $config->localDocumentRoot . $v, $v);
                        break;
                    case "D":
                        $content[] = sprintf("rm %s", $v);
                        break;
                }
            }
        }
        return $content;
    }

}
