<?php

namespace system\core\requests;

use system\core\abstracts\traits\patterns\Singleton;

/**
 *
 *
 * Class Get
 *
 * @package system\core\requests
 */
class Get
{
    use Singleton;

    private $request_get = [];

    protected function __construct()
    {
        $get = $_GET;

        if (true == is_array($get)) {
            foreach($get as $k_g => $g) {
                $this->request_get[$k_g] = $g;
            }
        }
    }

    /**
     * @return array
     */
    public function getRequest():array
    {
        return $this->request_get;
    }
}