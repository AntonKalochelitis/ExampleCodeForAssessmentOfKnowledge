<?php

namespace system\core\abstracts\mvc;

use system\core\Logger;

/**
 *
 *
 * Class MVCModel
 *
 * @package system\core\abstracts\mvc
 */
abstract class MVCModel
{
    protected $db;
    protected $logger;

    public function __construct()
    {
        $this->logger   =   Logger::getInstance();
        $this->db       =   $this->db_connect();
    }

    // TODO:
    public function db_connect()
    {

        return null;

    }
}