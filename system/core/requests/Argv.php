<?php

namespace system\core\requests;

use system\core\abstracts\traits\patterns\Singleton;

/**
 * Class Argv
 *
 * @package system\core\requests
 */
class Argv
{
    use Singleton;

    protected $requestArgv = [];

    protected function __construct()
    {
        if (true === is_array(ARGV)) {
            foreach (ARGV as $k_p => $p) {
                $exp = explode('=', $p);
                if (!empty($exp[1])) {
                    $this->requestArgv[$exp[0]] = $exp[1];
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getRequest(): array
    {
        return $this->requestArgv;
    }
}