<?php
use Aniart\Main\Models\Basket;
use Aniart\Main\Models\Order;
use Bitrix\Main\Config\Option;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @global $APPLICATION
 * @var array $arResult
 * @var AniartCheckoutComponent $component
 */
$component = $this->getComponent();
$order = $component->getOrderObject();
$coupon = array_shift($order->getCoupons());
$basket = $order->getBasket();
$isUserAuth = \CUser::IsAuthorized();

var_dump($component);
var_dump($order);
var_dump($coupon);
var_dump($basket);
var_dump($isUserAuth);
?>
