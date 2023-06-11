<?php

namespace method\test;

use system\core\abstracts\mvc\View;
use system\core\abstracts\mvc\WebController;
use system\core\Logger;

/**
 * Class Controller
 *
 * @package method\test
 */
class Controller extends WebController
{
    public function getRun(): void
    {
        View::set('Привет мир!');
    }
}