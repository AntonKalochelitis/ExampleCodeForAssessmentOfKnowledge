<?php
namespace method\test;

use system\core\Logger;

class Controller extends \system\core\abstracts\mvc\MVCController
{
    public function getRun():void
    {
        $this->view->setResult('Привет мир!');
    }
}