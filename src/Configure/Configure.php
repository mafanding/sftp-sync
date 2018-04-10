<?php
namespace SftpSync\Configure;

use SftpSync\Interfaces\ConfigureInterface;

class Configure implements ConfigureInterface
{

    protected static $instance;

    protected $autoCommit = false;

    protected $defaultCommitMessage = "";

    protected $syncExcludes = [];

    protected $commitExcludes = [];

    protected $remoteDocumentRoot = "";

    protected $localDocumentRoot = "";

    protected $batchFile = "";

    protected $remotePort = 0;

    protected $remoteIp = "";

    protected $remoteUser = "";

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
        $this->batchAssign($configs);
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

    protected function batchAssign($configs)
    {
        foreach ($configs as $k => $v) {
            $prop = lcfirst(str_replace(" ", "", ucwords(str_replace("_", " ", $k))));
            if (isset($this->$prop)) {
                $this->$prop = $v;
            }
        }
    }
    
}
