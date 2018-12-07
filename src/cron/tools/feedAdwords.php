<?php

include dirname(__FILE__) . '/../config.php';

@set_time_limit(0);
ini_set('memory_limit', '2048M');

require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

$startTime = microtime(true);

$export = new \Aniart\Main\Export\FeedAdwords(100, '/upload/feedAdwords.xml');
$export->init();

$endTime = microtime(true) - $startTime;
printf("time: %.4F sec. \n", $endTime);
/**/