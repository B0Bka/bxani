<?php
use Aniart\Main\Models\Basket;
use Aniart\Main\Models\Order;
use Bitrix\Main\Config\Option;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$component = $this->getComponent();
$order = $component->getOrderObject();
$coupon = array_shift($order->getCoupons());
$basket = $order->getBasket();
$props = $order->getPropsMeta('CODE');
$deliveryPrice = $order->getDeliveryPrice();
$orderPrice = $order->getBasePrice();
$orderDiscount = $order->getDiscountPrice();
$isUserAuth = $arResult['IS_AUTH'];
?>
<div id="sale_order" class="order-page step-order">
    <form method="post" action="" id="checkout_main_form" autocomplete="off">
    <?=bitrix_sessid_post();?>
    <input type="hidden" name="lang" value="<?=i18n()->lang()?>" />
    <input type="hidden" name="location_type" value="code" />
    
    <!-- Левая часть -->
    <div class="order-left <?//=(!$isUserAuth) ? "hide" : ""?>">
        <div class="order-part order-items">
            <div class="your-order">
                <div class="order-basket-title">
                    <div class="order-basket-top-sum">
                        <?=declOfNum($basket->itemsQuantity(), [i18n('ITEM1'), i18n('ITEM2'), i18n('ITEM3')])?> <?=i18n('ORDER_ON_SUM')?>: <span> <?=$order->getPrice(true)?></span>
                    </div>
                    <div class="order-basket-hide" onclick="App.checkout.hideBasket(this);"><?=i18n('SHOW')?></div>
                </div>
                <div class="order-basket-role">
                    <?foreach($basket->getItems() as $basketItem):?>
                        <?
                        $product = $basketItem->getProduct();
                        $arIds[] = "'".$product->getId()."'";
                        $discount = $basketItem->getDiscountPrice();
                        $price = $basketItem->getBasePrice();
                        $hasDiscount = $price > $discount && !empty($discount) ? true : false;
                        ?>
                        <div class="one-your-order basket-product">
                            <div class="one-your-order-thumb">

                                <?foreach($product->getAllImagesId(1) as $key=>$img):?>
                                    <?$img = $product->getMinPicture($img, 180, 270);?>
                                    <img
                                        src="<?=$img['src']?>"
                                        alt="<?=$product->getName()?>"
                                        title="<?=$product->getName()?>"
                                    />
                                <?endforeach;?>
                                <?if($hasDiscount):?>
                                    <div class="action-mark">%</div>
                                <?endif;?>
                                </div>
                                <div class="one-your-order-descr">
                                    <a href="<?=$product->getDetailPageUrl()?>" class="one-your-order-item-name">
                                        <?=$product->getName()?>
                                    </a>
                                    <span class="one-your-order-item-article">
                                        <?=i18n('VENDOR_CODE')?>: <?=$product->getArticle()?>
                                    </span>
                                    <div
                                        class="bask-delete"
                                        style="font-weight:bold;cursor:pointer;"
                                        title="<?=i18n('DELETE')?>"
                                    >
                                        <a data-id="<?=$basketItem->getId()?>" data-product_id="<?=$basketItem->getProductId()?>" href="javascript:void(0);">
                                            <i class="close" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div class="one-your-order-info">
                                        <div class="one-your-order-item-param">
                                            <?=i18n('COLOR')?>: <?=reset($product->getColor())?>
                                        </div>
                                        <div class="one-your-order-item-param">
                                            <?=i18n('SIZE')?>: <?=$basketItem->getSize()?>
                                        </div>
                                        <div class="one-your-order-item-param">
                                            <?=i18n('SHORT_COUNT')?>: <?=$basketItem->getQuantity()?>
                                        </div>
                                        <div class="one-your-order-item-param">
                                            <?=i18n('PRICE')?>: <?=$basketItem->getPrice(true)?>
                                            <?if($hasDiscount):?>
                                                <span class="one-your-order-item-discount-price"><?=$basketItem->getBasePrice(true)?></span>
                                            <?endif;?>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- Конец Один блок -->
                    <?endforeach;?>
                </div>
                <div class="order-sum step-2 hide">
                    <div class="order-card">
                        <?/*static loyality card?>
                        <? $APPLICATION->IncludeComponent(
                            'bitrix:main.include', '',
                            array(
                                'AREA_FILE_SHOW' => 'file',
                                'PATH' => SITE_TEMPLATE_PATH . '/include/loyality_card.php'
                            ),
                            false
                        ); ?>
                        <?static loyality card end*/?>
                    </div>
                    <div class="order-sum-price">
                        <div class="sum-row">
                            <div class="sum-price-name">
                                <?=i18n('BASKET_SUM', 'order')?>:
                            </div>
                            <div class="sum-price-num">
                                <?=$order->getBasePrice(true)?>
                            </div>
                        </div>
                        <?if($orderPrice > $orderDiscount && !empty($orderDiscount) > 0):?>
                            <div class="sum-row">
                                <div class="sum-price-name">
                                    <?=i18n('SUMM_DISCOUNT', 'order')?>
                                </div>
                                <div class="sum-price-num">
                                    <?=$order->getDiscountSum(true)?>
                                </div>
                            </div>
                        <?endif;?>
                        <div class="sum-row">
                            <div class="sum-price-name">
                                <?=i18n('DELIVERY', 'order')?>
                            </div>
                            <div class="sum-price-num">
                                <?=$deliveryPrice > 0 ? $order->getDeliveryPrice(true) : i18n('FREE_DELIVERY', 'order')?>
                            </div>
                        </div>
                        <div class="sum-row sum-total">
                            <div class="sum-price-name sum-price-total">
                                <?=i18n('TOTAL', 'order')?>:
                            </div>
                            <div class="sum-price-name-mob">
                                <?=i18n('TOTAL', 'order')?>:
                            </div>
                            <div class="sum-price-num sum-price-total">
                                <?=$order->getPrice(true)?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="action-bl hide">
                    <a href="#" id="show-coupon" style="visibility: <?=empty($arResult["ERROR_COUPON"]) && empty($coupon['COUPON']) ? 'visible' : 'hidden'?>"><?=i18n('PROMOCODE_EXIST', 'order')?></a>
                    <?foreach($component->getCouponsProps() as $prop):
                        $errors = $component->getPropErrors($prop['CODE']);
                    ?>
                        <div class="one-action-bl" style="display: <?=empty($arResult["ERROR_COUPON"]) && empty($coupon['COUPON']) ? 'none' : 'block'?>">
                            <div class="one-action-bl-in">
                                <input
                                    id="js_coupon_input"
                                    name="coupon"
                                    type="text"
                                    class="action-inp"
                                    placeholder=""
                                    value="<?=$coupon['COUPON']?>"
                                    <?=$coupon['COUPON']?'disabled':''?>
                                    autocomplete="off"
                                >

                                <?if($coupon['COUPON']):?>
                                    <a
                                        id="js_remove_coupon"
                                        class="action-bt"
                                    ><?=i18n('CANCEL')?></a>
                                <?else:?>
                                    <a
                                        id="js_apply_coupon"
                                        class="action-bt"
                                    ><?=i18n('ОК')?></a>
                                <?endif;?>

                                <input
                                    type="hidden"
                                    name="ORDER_PROP_<?=$prop['ID']?>"
                                    value="<?=$coupon['COUPON']?>"
                                />
                            </div>
                        </div>
                        <div class="err-text"><?=$arResult["ERROR_COUPON"]?></div>
                    <?endforeach?>
                </div>
            </div>
        </div>
    </div>
    <div class="order-right">
        <?
        if(!$isUserAuth):?>
            <div class="order-part order-log order-auth <?=empty($arResult['~ERROR_SORTED']) ? 'visible' : ''?>">
                <div class="log-form soc">
                    <div class="log-form-soc-title">
                        Войти через социальную сеть или почту
                    </div>
                    <div class="order-social-auth">
                        <?foreach($arResult['SOC_SERVICES'] as $soc):?>
                            <a href="javascript:void(0)" onclick="BX.util.popup('<?=$soc['URL']?>', 630, 450)"><img src="<?=$soc['IMG']?>"/></a>
                        <?endforeach;?>
                    </div>
                </div>
                <div class="log-form auth" id="auth-order-form">
                    <input type="hidden" name="do_authorize" value="Y">
                    <div class="one-log">
                        <input name="USER_LOGIN" type="text" data-req="1" placeholder="E-mail" value="<?=$arResult['USER_LOGIN']?>">
                    </div>
                    <div class="one-log">
                         <input name="USER_PASSWORD" type="password" data-req="1" placeholder="Пароль">
                    </div>
                    <?if(!empty($arResult['ERROR_AUTH'])):?>
                        <span class="system-error error-mes"><?=$arResult['ERROR_AUTH']?></span>
                    <?endif;?>

                </div>
                <div class="log-form log-buttons">
                    <div class="log-bt">
                        <input id="order-auth_submit" type="button" value="<?=i18n('LOGIN')?>">
                    </div>
                    <div class="log-bt">
                        <a class="first-buy" onclick="App.checkout.firstBuy(event);" ><?=i18n('FIRST_BUY', 'order')?></a>
                    </div>
                </div>
                <div class="log-form log-forg">
                    <a href="#" class="forg-bt" id="checkout_auth_forgot"><?=i18n('FORGOT_PASSWORD')?></a>
                </div>
                <div class="order-one-click log-form">
                    <div class="one-click-title"><span><?=i18n('OR')?></span></div>
                    <span class="one-click-description">
                        <?=i18n('FIRST_BUY_DESC', 'order')?>
                    </span>
                    <div class="log-buttons">
                        <div class="one-log">
                            <input id="one-click-number" class="input-phone" placeholder="+38 (___) ___-__-__" type="text" onclick="App.checkout.creanError(this);">
                        </div>
                        <div class="one-log">
                            <button class="border-orange" onclick="App.checkout.oneClick(event);"><?=i18n('FIRST_BUY_BT', 'order')?></button>
                        </div>
                    </div>
                </div>
            </div>
            <br style="clear: both">

        <?endif;?>
        <div class="order-part order-log authorised order-fields <?=$isUserAuth || !empty($arResult['~ERROR_SORTED'])  ? 'visible' : ''?>">
                <div class="order-form">
                    <?foreach($component->getUserProps() as $prop):
                        $errors = $component->getPropErrors($prop['CODE']);
                    if($prop['CODE'] == 'CITY'):
                    ?>
                    <div class="all-hide-radio a-checkout-delivery">
                        <div class="radio-hide" style="display: block;">
                            <div id="checkout_np_city" class="one-order-form">
                                    <input
                                        type="text"
                                        name="ORDER_PROP_<?=$props['CITY']['ID']?>"
                                        value="<?=current($props['CITY']['VALUE'])?>"
                                        style="<?=($errors?'background-color: pink;':'')?>"
                                        placeholder="<?=i18n('ORDER_CITY', 'order');?>"
                                        autocomplete="off"
                                    />
                                    <div class="err-text"><?=implode('<br />', $errors)?></div>
                            </div>
                        </div>
                    </div>
                    <?else:?>
                        <div
                            class="one-order-form a-checkout-user-prop"
                            data-id="<?=$prop['ID']?>"
                            data-code="<?=$prop['CODE']?>"
                        >
                            <input
                                type="text"
                                name="ORDER_PROP_<?=$prop['ID']?>"
                                value="<?=current($prop['VALUE'])?>"
                                style="<?=($errors?'background-color: pink;':'')?>"
                                <?if($prop["IS_PHONE"] == "Y"){?>
                                    class = 'phone'
                                <?}else{?>
                                    placeholder="<?=strtoupper($prop['NAME'])?>"
                                <?}?>
                                autocomplete="off"

                            />
                            <?if($errors):?>
                                <div class="error-mes" style="display:block;">
                                    <?=implode('<br />', $errors)?>
                                </div>
                            <?endif?>
                        </div>
                    <?endif;?>
                    <?endforeach?>

                    <div class="pay-radio">
                        <div class="one-order-form">
                            <select class="order-delivery-select">
                                <?foreach($arResult['~DELIVERY'] as $delivery):
                                    if($delivery->isShopStores() && !$delivery->isShopInCity(current($props['CITY']['VALUE'])))
                                        continue;
                                    ?>
                                    <option value="<?=$delivery->getId()?>" <?if($delivery->isChecked()):?>selected<?endif?>>
                                        <?=$delivery->getName()?>
                                    </option>
                                <?endforeach;?>
                            </select>
                        </div>
                    <?foreach($arResult['~DELIVERY'] as $delivery):
                    /**
                     * @var \Aniart\Main\Models\SaleDelivery $delivery
                     */
                    ?>
                        <div style="display:none" class="one-pay-radio a-checkout-delivery <?if($delivery->isChecked()):?>checked<?endif?>">
                            <label class="<?if($delivery->isChecked()):?>checked<?endif?>">
                                <input
                                        class="delivery-radio"
                                        type="radio"
                                        name="<?=$delivery->FIELD_NAME?>"
                                        value="<?=$delivery->getId()?>"
                                        <?if($delivery->isChecked()):?>checked<?endif?>
                                />
                                <span>
                                    <?=$delivery->getName()?>
                                </span>
                            </label>
                        </div>
                    <?endforeach;?>


                    <?foreach($arResult['~DELIVERY'] as $delivery):
                    /**
                     * @var \Aniart\Main\Models\SaleDelivery $delivery
                     */
                    ?>
                        <?if($delivery->isNewPostStores() && $delivery->isChecked()):
                            $props = $order->getPropsMeta('CODE');
                            $errors = $component->getPropErrors('CITY');
                        ?>
                        <div class="all-hide-radio a-checkout-delivery">
                            <div class="radio-hide" style="display: block;">
                                <div id="checkout_np_departments" class="one-order-form" style="display: <?=empty($arResult['NP_DEPARTMENTS']) ? 'none' : 'block'?>">
                                    <div class="radio-hide-select new-post">
                                        <div class="one-order-form">
                                            <select
                                                data-dep="<?=current($props['NP_DEPARTMENT']['VALUE'])?>"
                                                name="ORDER_PROP_<?=$props['NP_DEPARTMENT']['ID']?>"
                                            >
                                                <?if(!empty($arResult['NP_DEPARTMENTS'])):
                                                    foreach ($arResult['NP_DEPARTMENTS'] as $department):?>
                                                        <option value="<?=$department?>"><?=$department?></option>
                                                <?
                                                    endforeach;
                                                endif;?>
                                            </select>
                                        </div>
                                        <div class="err-text"><?=implode('<br />', $errors)?></div>
                                    </div>
                                </div>
                                <div class="radio-hide-descr">
                                    <div class="post-tit"></div>
                                    <div class="post-adr"></div>
                                    <div class="post-time"></div>
                                </div>
                            </div>
                        </div>
                        <?endif;?>

                        <?if($delivery->isNewPost() && $delivery->isChecked()):
                            $props = $order->getPropsMeta('CODE');
                            $errors = $component->getPropErrors('CITY');
                        ?>
                        <div class="all-hide-radio">
                        <div class="radio-hide" style="display: block;">

                            <div class="one-order-form">
                                <input
                                    type="text"
                                    name="ORDER_PROP_<?=$props['STREET']['ID']?>"
                                    value="<?=current($props['STREET']['VALUE'])?>"
                                    autocomplete="off"
                                    placeholder="<?=i18n('STREET')?>"
                                />
                                <div class="err-text"><?//=implode('<br />', $errors)?></div>
                            </div>
                            <div class="one-order-form">
                                <div class="order-form-50">
                                    <input
                                        type="text"
                                        name="ORDER_PROP_<?=$props['HOUSE']['ID']?>"
                                        value="<?=current($props['HOUSE']['VALUE'])?>"
                                        autocomplete="off"
                                        placeholder="<?=i18n('HOUSE')?>"
                                    />
                                </div>
                                <div class="order-form-50">
                                    <input
                                        type="text"
                                        name="ORDER_PROP_<?=$props['APARTMENT']['ID']?>"
                                        value="<?=current($props['APARTMENT']['VALUE'])?>"
                                        autocomplete="off"
                                        placeholder="<?=i18n('FLAT')?>"
                                    />
                                </div>
                            </div>
                        </div>
                        </div>
                        <?endif;?>

                        <?if($delivery->isShopStores() && $delivery->isChecked()):
                            $shops = $delivery->getShops(current($props['CITY']['VALUE']));
                        ?>
                        <div class="all-hide-radio">
                        <div class="radio-hide" style="display: block;">
                            <div class="radio-hide-select">
                                <div class="one-order-form">
                                    <select
                                        id="checkout_stores"
                                        name="ORDER_PROP_<?=$props['STORE_ADDRESS']['ID']?>"
                                    >
                                        <?
                                        foreach($shops as $shop):
                                            /**
                                             * @var \Aniart\Main\Models\Shop $shop
                                             */
                                        ?>
                                        <option
                                            class="a-checkout-store"
                                            value='<?=$shop->getName()?>'
                                        ><?=$shop->getName()?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        </div>
                        <?endif;?>

                    <?endforeach;?>

                    </div>

                    <div class="pay-radio">
                        <div class="order-tit">
                            <?=i18n('PAYSYSTEMS_TITLE', 'order')?>
                        </div>
                        <?foreach($arResult['~PAY_SYSTEM'] as $paySystem):
                        /**
                         * @var \Aniart\Main\Models\SalePaySystem $paySystem
                         */
                        ?>
                            <div class="one-pay-radio" data-id="<?=$paySystem->getId()?>">
                                <label>
                                    <input
                                        type="radio"
                                        name="PAY_SYSTEM_ID"
                                        value="<?=$paySystem->getId()?>"
                                        <?if($paySystem->isChecked()):?>checked<?endif?>
                                    />
                                    <span>
                                        <?=$paySystem->getName()?>
                                    </span>
                                </label>
                            </div>
                        <?endforeach;?>
                    </div>

                    <div class="one-order-form">
                        <textarea rows="3" name="ORDER_DESCRIPTION" placeholder="<?=i18n('ORDER_COMMENT', 'order');?>"><?=$component->getComment()?></textarea>
                    </div>
                    <div class="one-order-form">
                        <label class="no-call">
                            <input type="checkbox" name="ORDER_PROP_20" class="no-styler" value="Y" <?=$props['DONT_CALL']['VALUE'][0] == 'Y' ? 'checked' : ''?>> <span><?=i18n('ORDER_SURE', 'order');?></span>
                        </label>
                    </div>
                </div>

                <div class="bt-order">
                    <button class="border-orange" href="javascript:App.checkout.submit();">
                            <?if($order->getPaySystemId() == 3) echo i18n('TO_PAYMENT', 'order');
                                else echo i18n('TO_CHECKOUT', 'order');
                            ?>
                    </button>
                    <span class="order-oferta-desc"><?=i18n('ORDER_OFERTA', 'order');?></span>
                    <a class="order-oferta-link" href="/publichnaya_oferta/" target="_blank"><?=i18n('ORDER_OFERTA_LINK', 'order');?></a>
                </div>
        </div>
            <br style="clear: both">
    </div>
    </form>

    <script type="text/javascript">
        function orderInit(){
            App.CheckoutWidget.lang = '<?=i18n()->lang()?>';
            App.checkout = new App.CheckoutWidget($('#sale_order'), {
                isAuth: <?=intval($isUserAuth)?>,
                componentParams: '<?=$component->getSignedComponentParams()?>'
            });
            App.getPhoneSelect({
                object:$('.phone')
            });
            //App.authOrder =  new App.AuthFormWidget($('#sale_order'));
            jQuery.each(jQuery('.one-order-form textarea'), function() {
                var offset = this.offsetHeight - this.clientHeight;

                var resizeTextarea = function(el) {
                    jQuery(el).css('height', 'auto').css('height', el.scrollHeight + offset);
                };
                jQuery(this).on('keyup input', function() { resizeTextarea(this); });
            });

            App.Gtm.GetOrderShow(<?=$arResult['BASKET_GTM']?>);
        }
        $(document).ready(function(){
            orderInit();
        });
        var google_tag_params = {
            dynx_itemid: [<?=implode(',', $arIds)?>],
            dynx_pagetype: "conversionintent",
            dynx_totalvalue: <?=$order->getPrice()?>
        };
    </script>
</div>