<?php
namespace system\core\requests;

use system\core\traits\patterns\Singleton;

class Post
{
    use Singleton;

    private $request_post = [];

    protected function __construct($params = [])
    {
        $post = $_POST;

        if (true == is_array($post)) {
            foreach($post as $k_p => $p) {
                $this->request_post[$k_p] = $p;
            }
        }
    }

    /**
     * @return array
     */
    public function getRequest():array
    {
        return $this->request_post;
    }
}