<?php

// Check version php
if (version_compare(PHP_VERSION, '7.1', '<')) {
    echo 'At least PHP 7.1 is required to run this script!';
    exit(1);
}

// Ignore the user's disconnection and allows the script to be started constantly
// Игнорирует отключение пользователя и позволяет скрипту быть запущенным постоянно
ignore_user_abort(1);
set_time_limit(0);

// TODO: Set in config
// Error output
// Задаем вывод ошибок
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// the script started through the shell or web
// Определяем скрипт запущен через shell или web
$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) == 'cli' || empty($_SERVER['REMOTE_ADDR'])) {
    defined('IS_SHALL') or define('IS_SHALL', true);
    define('SEPARATOR', PHP_EOL);
    define('ARGV', $argv);
} else {
    defined('IS_SHALL') or define('IS_SHALL', false);
    define('SEPARATOR', '<br/>' . PHP_EOL);
}

// Set the time for the script execution
// Задаем метку начала выполнения скрипта
defined('START_SCRIPT') or define('START_SCRIPT', microtime(true));

// Set the root directory
// Задаем корневую дерикторию
defined('DEFAULT_DIR') or define('DEFAULT_DIR', realpath(dirname(__FILE__)));
defined('TEMP_DIR') or define('TEMP_DIR', realpath(dirname(__FILE__ ) . DIRECTORY_SEPARATOR . 'temp'));

// connectino autoload
// Подключаем Автолоадер
require_once DEFAULT_DIR . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'autoload.php';

// Start execution site
// Стартуем сайт
\system\Loader::getInstance()->InitWorkSpace();