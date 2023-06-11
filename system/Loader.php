<?php

namespace system;

use system\core\abstracts\mvc\View;
use system\core\abstracts\patterns\FactoryMethod;
use system\core\abstracts\traits\patterns\Singleton;
use system\core\abstracts\traits\TraitSetGetForClass;
use system\core\Logger;
use system\core\requests\Argv;
use system\core\requests\Get;
use system\core\requests\Post;

/**
 * Класс Loader обрабатывает методы связанные с началом загрузки странички
 * The loader class exposes methods related to triggering a page load
 *
 * Class Loader
 *
 * @package system
 */
class Loader
{
    use Singleton;
    use TraitSetGetForClass;

    protected $method = '';

    /**
     * @return void
     * @throws \Exception
     */
    public function InitWebWorkSpace(): void
    {
        $get = Get::getInstance()->getRequest();
        $post = Post::getInstance()->getRequest();

        if (!empty($post['method'])) {
            $this->method = $post['method'];
        } elseif (!empty($get['method'])) {
            $this->method = $get['method'];
        }

        if ($this->checkMethodIsEmpty($this->method)) {
            $methodObj = FactoryMethod::getInstance($this->method);

            echo View::get();
        } else {
            $exception_message = 'Method is Empty!';

            throw new \Exception($exception_message);
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function InitConsoleWorkSpace(): void
    {
        $argv = Argv::getInstance()->getRequest();

        if (!empty($argv['method'])) {
            $this->method = $argv['method'];
        }

        if ($this->checkMethodIsEmpty($this->method)) {
            $methodObj = FactoryMethod::getInstance($this->method);

            echo View::get();
        } else {
            $exception_message = 'Method is Empty!';

            throw new \Exception($exception_message);
        }
    }

    /**
     * @param string $method
     * @return bool
     */
    protected function checkMethodIsEmpty(string $method): bool
    {
        if (!empty($method)) {
            return true;
        } else {
            return false;
        }
    }
}