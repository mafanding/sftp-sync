<?php
namespace SftpSync\Git;

use SftpSync\Interfaces\ListInterface;

class GitList implements ListInterface
{

    protected $modified;

    protected $untracked;

    protected $deleted;

    protected $news;

    public function __construct($list = [])
    {
        $this->init();
        $this->parseList($list);
    }

    protected function init()
    {
        $this->modified = [];
        $this->untracked = [];
        $this->deleted = [];
        $this->news = [];
    }

    public function parseList($list)
    {
        foreach($list as $v) {
            /** 
             * First char means changes have been commited, second char means changes haven't been commited
             */
            $headString = substr($v, 0, 2);
            switch ($headString) {
                case " M":
                case "M ":
                case "MM":
                    array_push($this->modified, substr($v, 3));
                    break;
                case " D":
                case "D ":
                    array_push($this->deleted, substr($v, 3));
                    break;
                case "A ":
                case "AM":
                    array_push($this->news, substr($v, 3));
                    break;
                case "??":
                    array_push($this->untracked, substr($v, 3));
                    break;
            }
        }
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function getUntracked()
    {
        return $this->untracked;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function getNews()
    {
        return $this->news;
    }

    public function getAll()
    {
        return array_merge($this->modified, $this->untracked, $this->deleted, $this->news);
    }

}
