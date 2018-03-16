<?php

$sftp_sync = SftpSync::getInstance();
$sftp_sync->sync();

class SftpSync
{

    const SUCCESS = 0;

    const GET_MODIFIED_COUNT = "git status | grep modified: | wc -l";
    
    const GET_MODIFIED_LIST = "git status | grep modified:";

    const MODIFIED_PREFIX = " modified:   ";

    const SYNC_FILE = "sftp -P 52000 -b batch.out cpyybtest@114.55.126.18";

    const BATCH_FILE = "batch.out";

    protected $requirements = [
        "commands" => [
            "git",
            "grep",
            "wc",
            "sftp",
        ],
        "files" => [
            ".git",
        ],
    ];

    protected $preSync = "cd www/html/develop_ljm";

    protected $rootPath = "";

    protected static $instance = null;

    protected function __construct()
    {
        try {
            $this->checkRequirements();
            $this->rootPath = getcwd() . DIRECTORY_SEPARATOR;
        } catch (Exception $e) {
            $this->printDie(255, $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!is_null(self::$instance)) {
            return self::$instance;
        }
        self::$instance = new static;
        return self::$instance;
    }

    protected function checkRequirements()
    {
        array_walk($this->requirements["commands"], function ($v) {
            exec("which $v", $output, $code);
            if ($code !== 0) {
                throw new Exception("$v not found");
            }
        });
        array_walk($this->requirements["files"], function ($v) {
            if (!file_exists($v)) {
                throw new Exception("$v doesn't exists");
            }
        });
    }

    public function sync()
    {
        try {
            $modifiedList = $this->getModifiedList();
            $this->syncModifiedList($modifiedList);
        } catch (Exception $e) {
            if (file_exists(self::BATCH_FILE)) {
                @unlink(self::BATCH_FILE);
            }
            $this->printDie(255, $e->getMessage());
        } finally {
            @unlink(self::BATCH_FILE);
        }
    }

    protected function getModifiedList()
    {
        $list = [];
        exec(self::GET_MODIFIED_COUNT, $output, $code);
        if ($code !== self::SUCCESS) {
            throw new Exception("Failed to get modified count");
        }
        unset($output, $code);
        exec(self::GET_MODIFIED_LIST, $output, $code);
        if ($code !== self::SUCCESS) {
            throw new Exception("Failed to get modified list");
        }
        $list = $output;
        return $list;
    }

    protected function syncModifiedList($modifiedList = [])
    {
        if (empty($modifiedList)) {
            return true;
        }
        $batchContent[] = $this->preSync;
        foreach ($modifiedList as $v) {
            $filePath = substr($v, strlen(self::MODIFIED_PREFIX));
            $localPath = $this->rootPath . $filePath;
            $batchContent[] = sprintf("put %s %s", $localPath, $filePath);
        }
        file_put_contents(self::BATCH_FILE, implode(PHP_EOL, $batchContent));
        exec(self::SYNC_FILE, $output, $code);
        if ($code !== self::SUCCESS) {
            throw new Exception("Failed to sync file");
        }
        return true;
    }

    protected function printDie($code = 0, $str = "")
    {
        if (!empty($str)) {
            printf("%s\n", $str);
        }
        exit((int) $code);
    }

    protected function __clone()
    {
    }

}
