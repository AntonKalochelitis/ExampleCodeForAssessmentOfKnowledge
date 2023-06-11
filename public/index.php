<?php

use Symfony\Component\Dotenv\Dotenv;
use system\core\Logger;

use system\core\requests\Argv;
use system\core\requests\Get;
use system\core\requests\Post;

// Set the time for the script execution
// Задаем метку начала выполнения скрипта
defined('START_SCRIPT') or define('START_SCRIPT', microtime(true));

// TODO: Set in config
// Check version php
if (version_compare(PHP_VERSION, '7.1', '<')) {
    echo 'At least PHP 7.1 is required to run this script!';
    exit(1);
}

// TODO: Set in config
// Error output
// Задаем вывод ошибок
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Set the root directory
// Задаем корневую дерикторию
defined('DEFAULT_DIR') or define('DEFAULT_DIR', realpath(dirname(__FILE__) . '/..'));
defined('TEMP_DIR') or define('TEMP_DIR', realpath(DEFAULT_DIR . DIRECTORY_SEPARATOR . 'temp'));

define('SEPARATOR', '<br/>' . PHP_EOL);

// connection autoload
// Подключаем Автолоадер
require_once DEFAULT_DIR . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'autoload.php';
if (is_file(DEFAULT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
    require_once DEFAULT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
}

if (file_exists(DEFAULT_DIR . '/.env')) {
    $dotEnv = new Dotenv();
    $dotEnv->load(__DIR__ . '/.env');
}

try {
    // Start execution site
    // Стартуем сайт
    /** @var \system\Loader $system */
    $system = \system\Loader::getInstance();
    $system->InitWebWorkSpace();
} catch (\Exception $e) {
    Logger::getInstance()->getException($e);
}