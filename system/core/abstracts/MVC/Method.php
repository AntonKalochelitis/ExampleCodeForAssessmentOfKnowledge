<?php
namespace system\core\abstracts\MVC;

use system\core\Logger;
use system\core\traits\patterns\tSingleton;

class Method
{
    use tSingleton;

    protected $model;
    protected $view;
    protected $controller;

    public function getMethod()
    {
        return $this->controller;
    }

    public function setMethod(string $method):void
    {
        $varcModel          = static::getModelName($method);
        $varcView           = static::getViewName($method);
        $varcController     = static::getControllerName($method);

        $this->model        = new $varcModel();
        $this->view         = new $varcView();

        if (!$this->model instanceof \system\core\abstracts\MVC\MVC_Model) {
            $exception_message = $varcModel . ' is not a abstract \system\core\abstracts\MVC\MVC_Model';
            $e = new \Exception($exception_message);
            Logger::getInstance()->getException($e);
        }

        if (!$this->view instanceof \system\core\abstracts\MVC\MVC_View) {
            $exception_message = $varcView . ' is not a abstract \system\core\abstracts\MVC\MVC_View';
            $e = new \Exception($exception_message);
            Logger::getInstance()->getException($e);
        }

        $this->controller = new $varcController($this->model, $this->view);

        if (!$this->controller instanceof \system\core\abstracts\MVC\MVC_Controller) {
            $exception_message = $varcController . ' is not a abstract \system\core\abstracts\MVC\MVC_Controller';
            $e = new \Exception($exception_message);
            Logger::getInstance()->getException($e);
        }
    }

    protected static function getModelName(string $method):string
    {
        return static::getMethodNamespace($method) . '\cModel';
    }

    protected static function getViewName(string $method):string
    {
        return static::getMethodNamespace($method) . '\cView';
    }

    protected static function getControllerName(string $method):string
    {
        return static::getMethodNamespace($method) . '\cController';
    }

    protected static function getMethodNamespace(string $method):string
    {
        return '\method\\' . $method;
    }
}