<?php
namespace system\core\abstracts\mvc;

use system\core\Logger;

class MVCView
{
    protected $logger       =   null;
    protected $show_body    =   '';

    public function __construct()
    {
        $this->logger   =   Logger::getInstance();
    }

    public function setResult(string $body):void
    {
        $this->show_body = $body;
    }

    public function getShowResult():void
    {
        print_r($this->show_body);
        echo SEPARATOR;
    }
}