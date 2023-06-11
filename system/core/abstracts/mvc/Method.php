<?php

namespace system\core\abstracts\mvc;

use system\core\abstracts\traits\TraitSetGetForClass;
use system\core\Logger;
use system\core\abstracts\traits\patterns\Singleton;

/**
 * Class Method
 *
 * @package system\core\abstracts\mvc
 */
class Method
{
    use Singleton;
    use TraitSetGetForClass;

    protected $controller;

    public function getMethod()
    {
        return $this->controller;
    }

    public function setMethod(string $method): void
    {
        $varController = static::getControllerName($method);
        $this->controller = new $varController();

        if (
            !$this->controller instanceof \system\core\abstracts\interfaces\Controller
        ) {
            $exception_message = SEPARATOR;
            $exception_message .= $varController . ' is not a abstract \system\core\abstracts\mvc\WebController';
            $exception_message .= SEPARATOR;
            $exception_message .= $varController . ' is not a abstract \system\core\abstracts\mvc\ConsoleController';
            $e = new \Exception($exception_message);
            Logger::getInstance()->getException($e);
        }
    }

    protected static function getControllerName(string $method): string
    {
        return static::getMethodNamespace($method) . '\Controller';
    }

    protected static function getMethodNamespace(string $method): string
    {
        return '\method\\' . $method;
    }
}