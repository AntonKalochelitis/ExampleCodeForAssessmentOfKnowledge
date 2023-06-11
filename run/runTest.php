#!/usr/bin/env php
<?php

$dir = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$dirTemp = $dir . '..' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
$dirLog = $dirTemp . 'log' . DIRECTORY_SEPARATOR;

// print_r($dirTemp);
// exit();

$count_string = 10000000;
$char_from = 3;
$char_to = 5;

$result = shell_exec(
    'php -q '
    . $dir
    . './../bin/console'
    . ' '
    . 'method=test'
    . ' '
    . 'count_string='
    . $count_string
    . ' '
    . 'char_from='
    . $char_from
    . ' '
    . 'char_to='
    . $char_to
);
print_r($result);