<?php
namespace SftpSync;

use SftpSync\Interfaces\ConfigureInterface;

class Configure implements ConfigureInterface
{

    protected static $instance;

    protected $autoCommit;

    protected $defaultCommitMessage;

    protected $syncExcludes;

    protected $commitExcludes;

    protected $remoteDocumentRoot;

    protected $localDocumentRoot;

    protected $batchFile;

    protected $remotePort;

    protected $remoteIp;

    protected $remoteUser;

    protected function __construct($userDefined)
    {
        $defaultConfigs = [
            "auto_commit" => false,
            "default_commit_message" => "Auto committed by sftp-sync tools",
            "remote_document_root" => "/",
            "local_document_root" => CURRENT_WORK_DIR,
            "remote_port" => 22,
            "remote_ip" => "127.0.0.1",
            "remote_user" => "root",
            "batch_file" => "batch.tmp",
            "sync_excludes" => [],
            "commit_excludes" => [],
        ];
        if (empty($userDefined) || !file_exists($userDefined) || !is_readable($userDefined)) {
            $userDefinedConfigs = [];
        } else {
            $userDefinedConfigs = json_decode(file_get_contents($userDefined), true);
        }
        $configs = array_merge($defaultConfigs, $userDefinedConfigs);
        $this->autoCommit = $configs["auto_commit"];
        $this->defaultCommitMessage = $configs["default_commit_message"];
        $this->syncExcludes = $configs["sync_excludes"];
        $this->commitExcludes = $configs["commit_excludes"];
        $this->batchFile = $configs["batch_file"];
        $this->remotePort = $configs["remote_port"];
        $this->remoteIp = $configs["remote_ip"];
        $this->remoteUser = $configs["remote_user"];
        $this->remoteDocumentRoot = rtrim($configs["remote_document_root"], "/\\") . DIRECTORY_SEPARATOR;
        $this->localDocumentRoot = rtrim($configs["local_document_root"], "/\\") . DIRECTORY_SEPARATOR;
    }

    public static function load($userDefined = "")
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($userDefined);
        }
        return self::$instance;
    }

    public function __get($name)
    {
        return $this->$name ?? null;
    }

}
