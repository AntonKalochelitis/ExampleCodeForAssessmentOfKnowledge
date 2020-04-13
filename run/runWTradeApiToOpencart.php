#!/usr/bin/env php
<?php

$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR;
$dirTemp = $dir.'..'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
$dirLog = $dirTemp.'log'.DIRECTORY_SEPARATOR;
$method = 'wtradeApiToOpencart';

$result = shell_exec(
    '/opt/php72/bin/php -q '
   .$dir
   .'./../index.php'
   .' '
   .'method='.$method
);

file_put_contents($dirLog.time().'.'.$method.'.log', $result, FILE_APPEND);
//print_r($result);
