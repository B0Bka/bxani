<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var \Aniart\Main\Models\Order $order
 * @var AniartCheckoutComponent $component
 */
$order = $arResult['~ORDER'];
$component = $this->getComponent();
?>
<?if(!empty($arResult['ORDER']['PAYMENT_HTML'])) echo($arResult['ORDER']['PAYMENT_HTML']);
    else echo '<h1>Ваш заказ успешно оформлен</h1>'?>