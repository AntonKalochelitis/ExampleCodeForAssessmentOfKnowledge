<?php

namespace method\test;

use system\core\Logger;

/**
 * Class Controller
 *
 * @package method\test
 */
class Controller extends \system\core\abstracts\mvc\MVCController
{
    public function getRun():void
    {
        $this->view->setResult('Привет мир!');
    }
}