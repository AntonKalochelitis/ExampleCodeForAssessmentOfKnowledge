<?php

namespace system\core\traits\patterns;

trait Singleton
{
    /**
     * Защищаем от создания через new Class
     * @param array $params
     */
    protected function __construct(array $params = [])
    {/* ... @return Singleton */}

    /**
     * Защищаем от создания через клонирование
     */
    protected function __clone()
    {/* ... @return Singleton */}

    /**
     * Защищаем от создания через unserialize
     */
    protected function __wakeup()
    {/* ... @return Singleton */}

    protected static $instance    =   null;

    /**
     * @param array $params
     * @return Singleton
     */
    public static function getInstance(array $params = [])
    {
        return static::$instance === null ? static::$instance = new static($params) : static::$instance;
    }

    protected $variable           =   null;

    /**
     * @param $variable
     * @return null
     */
    function __get($variable)
    {
        if (isset($this->variable[$variable])) {
            return $this->variable[$variable];
        }

        return null;
    }

    /**
     * @param string $variable
     * @param mixed $val
     */
    function __set($variable = '', $val = null)
    {
        $this->variable[$variable] = $val;
    }
}