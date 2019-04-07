<?php
namespace method\test;

use system\core\Logger;

class Controller extends \system\core\abstracts\mvc\MVC_Controller
{
    public function getRun():void
    {
        $this->view->setResult('Привет мир!');
    }
}