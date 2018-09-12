<?php
namespace system;

use system\core\abstracts\patterns\aFactoryMethod;
use system\core\abstracts\patterns\aSingleton;
use system\core\Logger;
use system\core\requests\Argv;
use system\core\requests\Get;
use system\core\requests\Post;

class Loader extends aSingleton
{
    private $argv   =   null;
    private $get    =   null;
    private $post   =   null;

    public function InitWorkSpace():void
    {
        $method = null;

        if (true == IS_SHALL) {
            $this->argv = Argv::getInstance()->getRequest();
        } else {
            $this->get  = Get::getInstance()->getRequest();
            $this->post = Post::getInstance()->getRequest();
        }

        if (!empty($this->post['method'])) {
            $method = $this->post['method'];
        } elseif (!empty($this->get['method'])) {
            $method = $this->get['method'];
        } elseif (!empty($this->argv['method'])) {
            $method = $this->argv['method'];
        }

        if (!empty($method)) {
            aFactoryMethod::getInstance($method);
        } else {
            $exception_message = 'Method is Empty!';

            $e = new \Exception($exception_message);
            Logger::getInstance()->getException($e);
        }

    }
}