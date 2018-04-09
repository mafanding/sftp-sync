<?php
namespace SftpSync\Interfaces;

interface ListInterface
{

    public function getAll();

    public function getNews();

    public function getModified();

    public function getUntracked();

    public function getDeleted();

}
