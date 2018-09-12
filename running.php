#!/usr/bin/env php
<?php

$directory = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

$count_string   =   10000000;
$char_from      =   3;
$char_to        =   5;

$result = shell_exec('php -q ' . $directory . 'index.php' . ' ' . 'method=test' . ' ' . 'count_string=' . $count_string . ' ' . 'char_from=' . $char_from . ' ' . 'char_to=' . $char_to);
print_r($result);