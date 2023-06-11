<?php

namespace system\core\requests;

use system\core\abstracts\traits\patterns\Singleton;

/**
 * Class Get
 *
 * @package system\core\requests
 */
class Get
{
    use Singleton;

    protected $requestGet = [];

    protected function __construct()
    {
        if (true === is_array($_GET)) {
            foreach ($_GET as $k_g => $g) {
                $this->requestGet[$k_g] = $g;
            }
        }
    }

    /**
     * @return array
     */
    public function getRequest(): array
    {
        return $this->requestGet;
    }
}