<?php
namespace method\test;

use system\core\Logger;

class cController extends \system\core\abstracts\MVC\MVC_Controller
{
    public function getRun():void
    {
        $this->view->setResult('Привет мир!');
    }
}