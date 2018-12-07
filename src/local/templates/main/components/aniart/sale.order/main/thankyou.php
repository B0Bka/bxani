<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var \Aniart\Main\Models\Order $order
 * @var AniartCheckoutComponent $component
 */
?>
<style>
    .thankyou-page{
        padding: 10px;
    }
    .thankyou-photo{
        display: inline-block;
        float: left;
        padding: 20px;
    }
    .thankyou-info{
        display: inline-block;
    }
    .thankyou-info p, .thankyou-info ul{
        margin-left: 10px;
    }
    .thankyou-info .thankyou-row{
        padding-bottom: 20px;
    }
    .thankyou-info .thankyou-row.head{
        text-align: center;
    }
    .thankyou-info h1{
        font-size: 22px;
        padding-bottom: 10px;
    }
    .thankyou-info span{
        font-size: 16px;
        font-weight: bold;
        padding-bottom: 5px;
        display: block;
    }
    .thankyou-info .thankyou-info-status{
        font-size: 18px;
        font-weight: bold;
    }
    .thankyou-description{
        clear:both;
    }

</style>
<div class="thankyou-page">
    <?if($arResult['ORDER']['PHOTO']):?>
        <div class="thankyou-photo">
            <img src="<?=$arResult['ORDER']['PHOTO']['src']?>"/>
        </div>
    <?endif;?>
    <div class="thankyou-info">
        <?$orderStr = str_replace('#ORDER_ID#', $arResult['ORDER']['ACCOUNT_NUMBER'], i18n("ORDER_ACCEPT", 'order'));?>
        <div class="thankyou-row head">
            <h1><?=$arResult['ORDER']['FIO']?>, <?=i18n("THANK", 'order')?></h1>
            <p class="thankyou-info-status"><?=$orderStr?></p>
        </div>
        <div class="thankyou-row">
            <span><?=i18n("ORDER_DETAIL", 'order')?>:</span>
            <ul>
            <?foreach($arResult['ORDER']['BASKET'] as $item):
                $arIds[] = "'".$item['ID']."'";
            ?>
                <li><a href="<?=$item['URL']?>" target="_blank"><?=$item['NAME']?></a> - <?=$item['PRICE']?></li>
            <?endforeach?>
            </ul>
        </div>
        <div class="thankyou-row">
            <span><?=i18n("DELIVERY", 'order')?>:</span>
            <p> <?=$arResult['ORDER']['DELIVERY_NAME']?> <?=$arResult['ORDER']['DELIVERY_ADRESS']?></p>
            <p> <?=$arResult['ORDER']['PHONE']?></p>
        </div>
        <div class="thankyou-row">
            <span><?=i18n("PAYMENT", 'order')?>:</span>
            <p> <?=$arResult['ORDER']['PAYSYSTEM_NAME']?> - <?=$arResult['ORDER']['PAYED'] == 'Y' ? i18n("STATUS_PAYED", 'order') : i18n("STATUS_NOT_PAYED", 'order');?></p>
        </div>
    </div>
    <div class="thankyou-description">
        <p>Статус и сроки доставки вы можете уточнить у менеджера по телефону или в личном кабинете</p>
        <p>Ознакомиться с условиями доставки, оплаты и возврата товара вы можете <a href="/return_exchange/">по ссылке</a></p>
    </div>
</div>
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
<?$APPLICATION->IncludeComponent(
	"quetzal:tracking.order",
	"",
Array("ORDER_PARAM_TRANSACTION" => $arResult['ORDER']['ACCOUNT_NUMBER']),
false
);?>
