<?php
namespace SftpSync\Git;

use SftpSync\Interfaces\CommitInterface;
use SftpSync\Interfaces\ListInterface;
use SftpSync\Interfaces\ConfigureInterface;
use SftpSync\Interfaces\ShellInterface;
use SftpSync\Shell;

class GitCommit implements CommitInterface
{

    protected $shell;

    protected $config;

    protected $list;

    public function __construct(ListInterface $list, ConfigureInterface $config, ShellInterface $shell = null)
    {
        if (is_null($shell)) {
            $shell = new Shell;
        }
        $this->shell = $shell;
        $this->list = $list;
        $this->config = $config;
    }

    public function run()
    {
        $this->add()->commit();
    }

    protected function add()
    {
        $this->shell->git(sprintf("add %s", implode(" ", $this->list->getAll())));
        return $this;
    }

    protected function commit()
    {
        $this->shell->git(sprintf("commit -m '%s'", $this->config->defaultCommitMessage));
        return $this;
    }

}
