<?php

$run = 'run only in console. Stop program!';

if(!isset($_SERVER['SHELL']) || empty($_SERVER['SHELL'])) die($run);

if(empty($_SERVER['DOCUMENT_ROOT']))
{
    $pathParts = explode('/', dirname(__FILE__));
    array_pop($pathParts);
    $_SERVER['DOCUMENT_ROOT'] = implode('/', $pathParts);
}

define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_CHECK', true);
define('NO_KEEP_STATISTIC', true);
