<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var AniartProductsListComponent $component
 * @var \Aniart\Main\Models\Product[] $products
 */
$component = $this->getComponent();
$products  = $arResult['PRODUCTS'];

var_dump($arParams);
var_dump($component);
var_dump($products);
?>