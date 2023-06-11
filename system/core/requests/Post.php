<?php

namespace system\core\requests;

use system\core\abstracts\traits\patterns\Singleton;

/**
 * Class Post
 *
 * @package system\core\requests
 */
class Post
{
    use Singleton;

    protected $requestPost = [];

    protected function __construct()
    {
        if (true === is_array($_POST)) {
            foreach($_POST as $k_p => $p) {
                $this->requestPost[$k_p] = $p;
            }
        }
    }

    /**
     * @return array
     */
    public function getRequest():array
    {
        return $this->requestPost;
    }
}