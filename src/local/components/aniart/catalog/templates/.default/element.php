<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var CBitrixComponent $component
 */
$component = $this->getComponent();
$elementCode = $arResult['VARIABLES']['ELEMENT_CODE'];

var_dump($component);
var_dump($elementCode);