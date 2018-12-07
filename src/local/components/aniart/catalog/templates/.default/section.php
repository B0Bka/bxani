<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var AniartCatalogComponent $component
 * @var \Aniart\Main\Models\ProductSection $section
 */
$component = $this->getComponent();
$section = $component->getCurrentSection();

var_dump($arParams);
var_dump($arResult);
?>