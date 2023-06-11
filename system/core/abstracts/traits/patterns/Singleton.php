<?php

namespace system\core\abstracts\traits\patterns;

/**
 * Трейт используем для создания объекта Singleton
 * This trait is used to create a Singleton object
 *
 * Trait Singleton
 *
 * @package system\core\traits\patterns
 */
trait Singleton
{
    /**
     * Защищаем от создания через new Class
     * We protect from creation through new Class
     *
     * @param array $params
     */
    final protected function __construct(array $params = [])
    {/* ... @return Singleton */}

    /**
     * Защищаем от создания через клонирование
     * We protect from creation through cloning
     */
    final public function __clone()
    {/* ... @return Singleton */}

    /**
     * Защищаем от создания через unserialize
     * We protect from creation through unserialize
     */
    final public function __wakeup()
    {/* ... @return Singleton */}

    /**
     * @param array $params
     * @return Singleton
     */
    public function __invoke(array $params = [])
    {
        return self::getInstance($params);
    }

    protected static $instance = null;

    /**
     * @param array $params
     * @return Singleton
     */
    public static function getInstance(array $params = [])
    {
        return static::$instance === null ? static::$instance = new static($params) : static::$instance;
    }
}