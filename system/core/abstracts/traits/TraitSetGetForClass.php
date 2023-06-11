<?php

namespace system\core\abstracts\traits;
/**
 * Trait TraitSetGetForClass
 *
 * Класс работает с магическими методами __set __get
 *
 * @package core\abstracts\traits
 * @author Anton Kalochelitis fire.anton@gmail.com
 */
trait TraitSetGetForClass
{
    /**
     * Список переменных сохраненных в массиве
     *
     * @var array
     */
    protected $valueArray = [];

    /**
     * Задаем магическим методом переменную
     *
     * @param $key
     * @param $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->valueArray[$key] = $value;
    }

    /**
     * Получаем магическим методом переменную
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->valueArray[$key])) {
            return $this->valueArray[$key];
        }
    }

    /**
     * Существует ли магическая переменная
     *
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->valueArray[$key]);
    }

    /**
     * Уничтожение магической переменной
     *
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->valueArray[$key]);
    }
}
