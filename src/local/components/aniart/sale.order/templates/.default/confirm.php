<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var \Aniart\Main\Models\Order $order
 * @var AniartCheckoutComponent $component
 */
$order = $arResult['~ORDER'];
$basket = $order->getBasket();
$component = $this->getComponent();

var_dump($order);
var_dump($basket);
var_dump($component);
?>
