<?php

namespace system\core;

use system\core\abstracts\traits\patterns\Singleton;

/**
 *
 *
 * Class Logger
 *
 * @package system\core
 */
class Logger
{
    use Singleton;

    private static $debug = true;

    /**
     * @param \Exception $e
     */
    public function getException(\Exception $e):void
    {
        echo SEPARATOR;
        print_r('File: ' . $e->getFile());
        echo SEPARATOR;
        print_r('Line: ' . $e->getLine());
        echo SEPARATOR;
        print_r('Message: ' . $e->getMessage());
        echo SEPARATOR;
        echo 'Trace: ';
        echo SEPARATOR;
        $traces = explode("\n", $e->getTraceAsString());
        foreach($traces as $trace) {
            echo $trace;
            echo SEPARATOR;
        }
        echo SEPARATOR;

        exit();
    }

    public function p_debug(string $message = '', bool $exit = false):void
    {
        if (true == self::$debug) {

            $trace = debug_backtrace();

            if (!empty($message)) {

                echo '<pre>';
                print_r($message);
                echo '</pre>';

                echo '<pre>';
                print_r($trace);
                echo '</pre>';

                if (true == $exit) {
                    exit();
                }

            } else {
                echo '<pre>';
                print_r('message is Empty');
                echo '</pre>';

                echo '<pre>';
                print_r($trace);
                echo '</pre>';
            }

        }
    }

    private function log(string $message = ''):void
    {
        $PathToDirLog = realpath(DEFAULT_DIR . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR);

        if (!is_dir($PathToDirLog)) mkdir($PathToDirLog);

        $PathToFileLog = $PathToDirLog . 'core.log';

        $log = fopen ($PathToFileLog, 'a');
        fwrite ($log, PHP_EOL . 'time: ' . time() . ': ' . $message);
        fclose ($log);
    }

    public function notifyAndLog(array $config = [], string $message = ''):void
    {

        self::p_debug($message);
//        self::sendMail($config, $message); // TODO: I need to do
        self::log($message);

    }
}
