<?php

require_once "vendor/autoload.php";

define("SFTP_SYNC_VERSION", "1.0.3");
define("CURRENT_WORK_DIR", getcwd() . DIRECTORY_SEPARATOR);
define("DEFAULT_CONFIG_FILE", CURRENT_WORK_DIR . "sftp-sync.json");

\SftpSync\SftpSync::run();
