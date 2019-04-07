<?php
namespace system\core\abstracts\mvc;

use system\core\Logger;
use system\core\traits\patterns\Singleton;

class Method
{
    use Singleton;

    protected $model;
    protected $view;
    protected $controller;

    public function getMethod()
    {
        return $this->controller;
    }

    public function setMethod(string $method):void
    {
        $varModel          = static::getModelName($method);
        $varView           = static::getViewName($method);
        $varController     = static::getControllerName($method);

        $this->model        = new $varModel();
        $this->view         = new $varView();

        if (!$this->model instanceof \system\core\abstracts\mvc\MVC_Model) {
            $exception_message = $varModel . ' is not a abstract \system\core\abstracts\mvc\MVC_Model';
            $e = new \Exception($exception_message);
            Logger::getInstance()->getException($e);
        }

        if (!$this->view instanceof \system\core\abstracts\mvc\MVC_View) {
            $exception_message = $varView . ' is not a abstract \system\core\abstracts\mvc\MVC_View';
            $e = new \Exception($exception_message);
            Logger::getInstance()->getException($e);
        }

        $this->controller = new $varController($this->model, $this->view);

        if (!$this->controller instanceof \system\core\abstracts\mvc\MVC_Controller) {
            $exception_message = $varController . ' is not a abstract \system\core\abstracts\mvc\MVC_Controller';
            $e = new \Exception($exception_message);
            Logger::getInstance()->getException($e);
        }
    }

    protected static function getModelName(string $method):string
    {
        return static::getMethodNamespace($method) . '\Model';
    }

    protected static function getViewName(string $method):string
    {
        return static::getMethodNamespace($method) . '\View';
    }

    protected static function getControllerName(string $method):string
    {
        return static::getMethodNamespace($method) . '\Controller';
    }

    protected static function getMethodNamespace(string $method):string
    {
        return '\method\\' . $method;
    }
}