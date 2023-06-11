<?php

namespace system\core\abstracts\mvc;

use system\core\abstracts\interfaces\Controller;
use system\core\Logger;
use system\core\requests\Argv;

abstract class ConsoleController implements Controller
{
    protected $argv = null;
    protected $logger = null;

    public function __construct()
    {
        $this->argv = Argv::getInstance()->getRequest();

        $this->logger = Logger::getInstance();

        $this->getRun();
    }

    abstract function getRun(): void;
}