<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var CBitrixComponent $component
 */

$APPLICATION->SetPageProperty('pageClass', '');

$component = $this->getComponent();
$elementCode = $arResult['VARIABLES']['ELEMENT_CODE'];

//new \dBug($component, '', true);
//new \dBug($elementCode, '', true);


$APPLICATION->IncludeComponent(
    'aniart:product.detail', 
    'main', 
    [
        'ELEMENT_CODE' => $elementCode,
        'AJAX_MODE' => 'Y',
        'AJAX_OPTION_HISTORY' => 'N',
        'AJAX_OPTION_JUMP' => 'N',
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
    ], 
    $component
);

$APPLICATION->IncludeComponent(
    "aniart:viewed.products",
    "main",
     [
        "ELEMENT_CODE" => $elementCode,
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME']
    ],
    $component
);