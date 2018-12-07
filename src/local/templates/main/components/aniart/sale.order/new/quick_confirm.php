<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<p class="thankyou-page">
    <span class="mobile-text-row"><?=i18n('THANK_TITLE', 'order')?></span>
    <?=i18n('THANK_PAGE', 'order', '', ['#ID#' => $arResult['ORDER']['ACCOUNT_NUMBER']])?>
</p>

<?
//запустить скрипт один раз
if(empty($_SESSION['GTM_'.$arResult['ORDER']['ACCOUNT_NUMBER']])):?>
<script>
    $(document).ready(function(){
        App.Gtm.GetOrderSuccess(<?=$arResult['ORDER_GTM']?>);
    });
    var google_tag_params = {
        dynx_itemid: [<?=implode(',', $arIds)?>],
        dynx_pagetype: "conversion",
        dynx_totalvalue: <?=$arResult['ORDER']['PRICE']?>
    };
</script>
<?
$_SESSION['GTM_'.$arResult['ORDER']['ACCOUNT_NUMBER']] = 'SHOWED';
endif;
?>
<?
$APPLICATION->IncludeComponent(
    "bitrix:catalog.bigdata.products",
    "basket",
    array(
        "TITLE" => i18n('BASKET_RECOMMEND_TITLE'),
        "COMPONENT_TEMPLATE" => ".default",
        "RCM_TYPE" => "any_personal",
        "ID" => $_REQUEST["PRODUCT_ID"],
        "IBLOCK_TYPE" => "1c_catalog",
        "IBLOCK_ID" => "2",
        "SHOW_FROM_SECTION" => "N",
        "SECTION_ID" => "",
        "SECTION_CODE" => "",
        "SECTION_ELEMENT_ID" => "",
        "SECTION_ELEMENT_CODE" => "",
        "DEPTH" => "2",
        "HIDE_NOT_AVAILABLE" => "N",
        "SHOW_DISCOUNT_PERCENT" => "Y",
        "PRODUCT_SUBSCRIPTION" => "N",
        "SHOW_NAME" => "Y",
        "SHOW_IMAGE" => "Y",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "PAGE_ELEMENT_COUNT" => "20",
        "LINE_ELEMENT_COUNT" => "3",
        "TEMPLATE_THEME" => "blue",
        "DETAIL_URL" => "",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "CACHE_GROUPS" => "Y",
        "SHOW_OLD_PRICE" => "N",
        "PRICE_CODE" => array(
            0 => "BASE",
            1 => "DISCOUNTS",
            2 => "PURCHASE",
            3 => "Розничная",
            4 => "Розничная (Инет.Маг.№ 12)",
        ),
        "SHOW_PRICE_COUNT" => "1",
        "PRICE_VAT_INCLUDE" => "Y",
        "CONVERT_CURRENCY" => "N",
        "BASKET_URL" => "/personal/basket.php",
        "ACTION_VARIABLE" => "action_cbdp",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "USE_PRODUCT_QUANTITY" => "N",
        "SHOW_PRODUCTS_2" => "Y",
        "PROPERTY_CODE_2" => array(
            0 => "MORE_PHOTO_ELITE",
            1 => "",
        ),
        "CART_PROPERTIES_2" => array(
            0 => "",
            1 => "",
        ),
        "ADDITIONAL_PICT_PROP_2" => "MORE_PHOTO_ELITE",
        "LABEL_PROP_2" => "NOVELTY",
        "PROPERTY_CODE_3" => array(
            0 => "",
            1 => "",
        ),
        "CART_PROPERTIES_3" => array(
            0 => "",
            1 => "",
        ),
        "ADDITIONAL_PICT_PROP_3" => "MORE_PHOTO",
        "OFFER_TREE_PROPS_3" => array(
        )
    ),
    false
);
?>