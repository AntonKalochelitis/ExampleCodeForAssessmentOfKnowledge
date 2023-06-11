<?php

namespace system\core\abstracts\mvc;

use system\core\abstracts\interfaces\Controller;
use system\core\requests\Get;
use system\core\requests\Post;
use system\core\Logger;

/**
 * Class WebController
 *
 * @package system\core\abstracts\mvc
 */
abstract class WebController implements Controller
{
    protected $get = null;
    protected $post = null;
    protected $logger = null;

    public function __construct()
    {
        $this->get = Get::getInstance()->getRequest();
        $this->post = Post::getInstance()->getRequest();

        $this->logger = Logger::getInstance();

        $this->getRun();
    }

    abstract function getRun(): void;
}