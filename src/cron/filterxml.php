<?php
if(empty($_SERVER['DOCUMENT_ROOT']))
{
    $pathParts = explode('/', dirname(__FILE__));
    array_pop($pathParts);
    $_SERVER['DOCUMENT_ROOT'] = implode('/', $pathParts);
}


define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_CHECK', true);
define('NO_KEEP_STATISTIC', true);

@set_time_limit(0);
ini_set('memory_limit', '2048M');

require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');
$startTime = microtime(true);

if(Bitrix\Main\Loader::includeModule('seo.filter'))
{
    $facetSmartFilter = new Seo\Filter\XmlFilter\XmlMain(1);
    $facetSmartFilter->init();
}

$endTime = microtime(true) - $startTime;
printf("time: %.4F sec. \n", $endTime);