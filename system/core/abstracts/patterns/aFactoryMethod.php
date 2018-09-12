<?php
namespace system\core\abstracts\patterns;

use system\core\abstracts\MVC\Method;

class aFactoryMethod
{
    protected static $controller = null;

    public static function getInstance(string $method)
    {
        static::setController($method);

        return static::getController();
    }

    protected static function setController(string $method):void
    {
        Method::getInstance()->setMethod($method);

        static::$controller = Method::getInstance()->getMethod();
    }

    protected static function getController()
    {
        return static::$controller;
    }
}