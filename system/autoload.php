<?php

spl_autoload_register('autoload');

/**
 * Autoload start function
 * Функция запуска автоподключения файлов классов
 *
 * @param string $class_name
 */
function autoload($class_name = '')
{
    if (!empty($class_name)) {
        $class_path = str_replace("\\", DIRECTORY_SEPARATOR, $class_name);

        try {
            $path_to_file = DEFAULT_DIR . DIRECTORY_SEPARATOR . $class_path . '.php';

            if (!is_file($path_to_file)) {
                $exception_message = 'File is missing:' . $path_to_file;

                $e = new \Exception($exception_message);
                \system\core\Logger::getInstance()->getException($e);
            }

            require_once($path_to_file);
        } catch (\Exception $e) {
            \system\core\Logger::getInstance()->getException($e);
        }
    }
}