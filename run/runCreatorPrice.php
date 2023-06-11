#!/usr/bin/env php
<?php

$dir = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$dirTemp = $dir . '..' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
$dirLog = $dirTemp . 'log' . DIRECTORY_SEPARATOR;

$result = shell_exec(
    '/opt/php72/bin/php -q '
    . $dir
    . './../bin/console'
    . ' '
    . 'method=creatorPrice'
);
print_r($result);