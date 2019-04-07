<?php
namespace system\core\requests;

use system\core\traits\patterns\Singleton;

class Argv
{
    use Singleton;

    private $request_argv = [];

    protected function __construct(array $params = [])
    {

        $vars = ARGV;

        if (true == is_array($vars)) {

            foreach($vars as $k_p=>$p) {
                $exp = explode('=', $p);
                if (!empty($exp[1])) {
                    $this->request_argv[$exp[0]] = $exp[1];
                }
            }

        }

    }

    public function getRequest():array
    {
        return $this->request_argv;
    }
}