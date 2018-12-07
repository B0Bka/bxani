<?php
define('NO_KEEP_STATISTIC', true);
require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

$request = $_REQUEST;

$params = json_decode($request['params'], true);

if($request['ajax_mod'] == 'Y')
{
    $params['AJAX_MOD'] = 'Y';
}

//init component
$APPLICATION->IncludeComponent(
    'aniart:basket',
    $request['template'],
    $params
);

    
?>