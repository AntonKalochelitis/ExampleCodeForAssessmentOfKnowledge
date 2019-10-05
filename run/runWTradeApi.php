#!/usr/bin/env php
<?php

$directory = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

$result = shell_exec(
    '/opt/php72/bin/php -q '
    . $directory
    . 'index.php'
    . ' '
    . 'method=wtradeApi'
);
print_r($result);
