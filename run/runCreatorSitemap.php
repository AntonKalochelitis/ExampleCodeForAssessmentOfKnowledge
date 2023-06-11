#!/usr/bin/env php
<?php

$DS = DIRECTORY_SEPARATOR;

$dir = realpath(dirname(__FILE__)) . $DS;
$dirTemp = $dir . '..' . $DS . 'temp' . $DS;
$dirLog = $dirTemp . 'log' . $DS;

$result = shell_exec(
    '/opt/php72/bin/php -q '
    . $dir
    . './../bin/console'
    . ' '
    . 'method=creatorSitemap'
);
file_put_contents($dirLog . '..' . $DS . '..' . $DS . '..' . $DS . '..' . $DS . 'sitemap.xml', $result);