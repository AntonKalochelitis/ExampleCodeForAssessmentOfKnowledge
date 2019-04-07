<?php
namespace system\core\abstracts\mvc;

use system\core\requests\Argv;
use system\core\requests\Get;
use system\core\requests\Post;
use system\core\Logger;

abstract class MVCController
{
    protected $argv     =   null;
    protected $get      =   null;
    protected $post     =   null;

    protected $logger   =   null;
    protected $model    =   null;
    protected $view     =   null;

    public function __construct(MVCModel $model, MVCView $view)
    {
        if (true == IS_SHALL) {
            $this->argv = Argv::getInstance()->getRequest();
        } else {
            $this->get  = Get::getInstance()->getRequest();
            $this->post = Post::getInstance()->getRequest();
        }

        $this->logger   = Logger::getInstance();
        $this->model    = $model;
        $this->view     = $view;

        $this->getRun();

        $this->getShowResult();
    }

    abstract function getRun():void ;

    public function getShowResult():void
    {
        print_r($this->view->getShowResult());
    }
}