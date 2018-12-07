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
$props = $order->getPropsMeta('CODE');
?>

<div id="sale_order" class="order-page <?//=($isUserAuth?'already-log':'')?>">
    <form method="post" action="" id="checkout_main_form" autocomplete="off">
    <?=bitrix_sessid_post();?>
    <input type="hidden" name="lang" value="<?=i18n()->lang()?>" />
    <input type="hidden" name="location_type" value="code" />
    
    <!-- Левая часть -->
    <div class="order-left">

        <!-- Ваш заказ -->
        <div class="order-part order-items">
            <div class="order-tit">
                <?=i18n('YOUR_ORDER', 'order')?>
            </div>

            <!-- Кнопка продолжить для моб и планшета -->
            <div class="next-bt-order">
                <a class="border-black">
                    <?=i18n('CONTINUE', 'order')?>
                </a>
            </div>
            <!-- Конец Кнопка продолжить для моб и планшета -->

            <div class="your-order">
            <?foreach($basket->getItems() as $basketItem):?>
                <?
                $product = $basketItem->getProduct();
                $arIds[] = "'".$product->getId()."'";
                ?>
                <!-- Один блок -->
                <div class="one-your-order basket-product">
                    <div class="one-your-order-thumb">
                        
                    <?foreach($product->getAllImagesId(1) as $key=>$img):?>
                        <?$img = $product->getMinPicture($img);?>
                        <img 
                            src="<?=$img['src']?>" 
                            alt="<?=$product->getName()?>" 
                            title="<?=$product->getName()?>"
                        />
                    <?endforeach;?>
                        
                    </div>
                    <div class="one-your-order-descr">
                        <a href="<?=$product->getDetailPageUrl()?>">
                            <?=$product->getName()?>
                        </a>
                        <div 
                            class="bask-delete" 
                            style="font-weight:bold;cursor:pointer;" 
                            title="<?=i18n('DELETE')?>"
                        >
                            <a data-id="<?=$basketItem->getId()?>" data-product_id="<?=$basketItem->getProductId()?>" href="javascript:void(0);">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="one-your-order-info">
                            <?/*Не выводить количестов товаров
                            <div class="one-your-order-num">
                                <?=$basketItem->getQuantity()?> <?=i18n('SHT_SHORT')?>
                            </div>
                            */?>
                            <div class="one-your-order-price">
                                <?=$basketItem->getPrice(true)?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Конец Один блок -->
            <?endforeach;?>

                <div class="order-sum-price">
				    <?if($order->getDeliveryPrice() > 0):?>
                    <div class="sum-row">
                        <div class="sum-price-name">
                            <?=i18n('BASKET_SUM', 'order')?>:
                        </div>
                        <div class="sum-price-num">
                            <?=$order->getBasketPrice(true)?>
                        </div>
                    </div>
                    <div class="sum-row">
                        <div class="sum-price-name">
                            <?=$order->getDelivery()->getName()?>:
                        </div>
                        <div class="sum-price-num">
                            <?=$order->getDeliveryPrice(true)?>
                        </div>
                    </div>
                    <?endif;?>
                    <div class="sum-row">
                        <div class="sum-price-name">
                            <?=i18n('TO_PAY', 'order')?>:
                        </div>
                        <div class="sum-price-name-mob">
                            <?=i18n('TOTAL', 'order')?>:
                        </div>
                        <div class="sum-price-num">
                            <?=$order->getPrice(true)?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <!-- Конец Ваш заказ  -->

        <!-- Уже покупали / Новый покупатель -->
        <div class="order-part order-log">
            <?if(!$isUserAuth):?>
                <div class="order-tit">
                    Уже покупали у нас?
                </div>
                <div
                    class="action-bt"
                    style="margin:-10px 0 20px;"
                    data-toggle="modal"
                    data-target="#myModal"
                ><?=i18n('LOGIN')?></div>

                <div class="order-tit">
                    Новый покупатель?
                </div>
            <?endif;?>
                <div class="order-form">
                    <?foreach($component->getUserProps() as $prop):
                        $errors = $component->getPropErrors($prop['CODE']);
                    ?>
                        <div
                            class="one-order-form a-checkout-user-prop"
                            data-id="<?=$prop['ID']?>"
                            data-code="<?=$prop['CODE']?>"
                        >
                            <div class="one-order-tit">
                                <?=$prop['NAME']?>
                            </div>

                            <input
                                type="text"
                                name="ORDER_PROP_<?=$prop['ID']?>"
                                value="<?=current($prop['VALUE'])?>"
                                style="<?=($errors?'background-color: pink;':'')?>"
                                <?if($prop["IS_PHONE"] == "Y"){?>
                                    class = 'phone'
                                <?}?>
                                autocomplete="new-password"
                            />
                            <?if($errors):?>
                                <div class="error-mes" style="display:block;">
                                    <?=implode('<br />', $errors)?>
                                </div>
                            <?endif?>
                        </div>
                    <?endforeach?>
                    <div class="one-order-tit">
                        Комментарий
                    </div>
                    <div class="one-order-form">
                        <textarea rows="3" name="ORDER_DESCRIPTION"><?=$component->getComment()?></textarea>
                    </div>
                </div>

                <!-- Кнопка продолжить для моб и планшета -->
                <div class="next-bt-order">
                    <a class="border-black">
                        Продолжить
                    </a>
                </div>
                <!-- Конец Кнопка продолжить для моб и планшета -->
        </div>
        <!-- Конец Уже покупали / Новый покупатель -->

    </div>
    <!-- Конец Левая часть -->

    <!-- Правая часть -->
    <div class="order-right">
        <!-- Способ оплаты -->
        <div class="order-part order-pays">
            <div class="order-tit">
                <?=i18n('PAYSYSTEMS_TITLE', 'order')?>
            </div>

            <div class="pay-radio">
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

            <div class="action-bl">
            <?foreach($component->getCouponsProps() as $prop):
                $errors = $component->getPropErrors($prop['CODE']);
            ?>
                <div class="one-action-bl">
                    <input
                        id="js_coupon_input"
                        name="coupon"
                        type="text"
                        class="action-inp"
                        placeholder="<?=$prop['NAME']?>"
                        value="<?=$coupon['COUPON']?>"
                        <?=$coupon['COUPON']?'disabled':''?>
                        autocomplete="new-password"
                    >
                    <div class="err-text"><?=$arResult["ERROR_COUPON"]?></div>
                    <?if($coupon['COUPON']):?>
                        <a
                            id="js_remove_coupon"
                            class="action-bt"
                        ><?=i18n('CANCEL')?></a>
                    <?else:?>
                        <a
                            id="js_apply_coupon"
                            class="action-bt"
                        ><?=i18n('USE')?></a>
                    <?endif;?>
                    <input
                        type="hidden"
                        name="ORDER_PROP_<?=$prop['ID']?>"
                        value="<?=$coupon['COUPON']?>"
                    />
                </div>
            <?endforeach?>
            </div>

            <?/*?>
            <div class="action-bl">
                <div class="one-action-bl">
                    <input type="text" class="action-inp" placeholder="Введите промокод">
                    <button class="action-bt">
                        <?=i18n('USE')?>
                    </button>
                </div>

                <div class="one-action-bl">
                    <input type="text" class="action-inp" placeholder="Введите номер подарочной карты">
                    <button class="action-bt">
                        <?=i18n('USE')?>
                    </button>
                </div>
            </div>
            <?*/?>

            <!-- Кнопка продолжить для моб и планшета -->
                <div class="next-bt-order">
                    <a class="border-black">
                        Продолжить
                    </a>
                </div>
                <!-- Конец Кнопка продолжить для моб и планшета -->

        </div>
        <!-- Конец Способ оплаты -->

        <!-- Способ доставки -->
        <div class="order-part order-del">
            <div class="order-tit">
                <?=i18n('DELIVERY_TITLE', 'order')?>
            </div>

            <div class="pay-radio">

            <?foreach($arResult['~DELIVERY'] as $delivery):
            /**
             * @var \Aniart\Main\Models\SaleDelivery $delivery
             */
            ?>
                <div class="one-pay-radio a-checkout-delivery <?if($delivery->isChecked()):?>checked<?endif?>">
                    <label class="<?if($delivery->isChecked()):?>checked<?endif?>">
                        <input
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
                    <div id="checkout_np_city" class="one-order-form">
                        <div class="one-order-tit">
                            <?=i18n('YOUR_CITY', 'order')?>
                        </div>
                        <input
                            type="text"
                            name="ORDER_PROP_<?=$props['CITY']['ID']?>"
                            value="<?=current($props['CITY']['VALUE'])?>"
                            style="<?=($errors?'background-color: pink;':'')?>"
                            placeholder="Начните вводить название города"
                            autocomplete="new-password"
                        />
                        <div class="err-text"><?=implode('<br />', $errors)?></div>
                    </div>
                    <div id="checkout_np_departments" class="one-order-form" style="display:none">
                        <div class="one-order-tit">
                            <?=i18n('DEPARTMENT', 'order')?>
                        </div>
                        <div class="radio-hide-select new-post">
                            <select
                                data-dep="<?=current($props['NP_DEPARTMENT']['VALUE'])?>"
                                name="ORDER_PROP_<?=$props['NP_DEPARTMENT']['ID']?>"
                            ></select>
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

                    <div id="checkout_np_city" class="one-order-form">
                        <div class="one-order-tit">
                            <?=i18n('YOUR_CITY', 'order')?>
                        </div>
                        <input
                            type="text"
                            name="ORDER_PROP_<?=$props['CITY']['ID']?>"
                            value="<?=current($props['CITY']['VALUE'])?>"
                            autocomplete="new-password"
                            style="<?=($errors?'background-color: pink;':'')?>"
                        />
                        <div class="err-text"><?=implode('<br />', $errors)?></div>
                    </div>
                    <div class="one-order-form">
                        <div class="one-order-tit">
                            <?=i18n('STREET')?>
                        </div>
                        <input
                            type="text"
                            name="ORDER_PROP_<?=$props['STREET']['ID']?>"
                            value="<?=current($props['STREET']['VALUE'])?>"
                            autocomplete="new-password"
                        />
                        <div class="err-text"><?//=implode('<br />', $errors)?></div>
                    </div>
                    <div class="one-order-form">
                        <div class="order-form-50">
                            <div class="one-order-tit">
                                <?=i18n('HOUSE')?>
                            </div>
                            <input
                                type="text"
                                name="ORDER_PROP_<?=$props['HOUSE']['ID']?>"
                                value="<?=current($props['HOUSE']['VALUE'])?>"
                                autocomplete="new-password"
                            />
                        </div>
                        <div class="order-form-50">
                            <div class="one-order-tit">
                                <?=i18n('FLAT')?>
                            </div>
                            <input
                                type="text"
                                name="ORDER_PROP_<?=$props['APARTMENT']['ID']?>"
                                value="<?=current($props['APARTMENT']['VALUE'])?>"
                                autocomplete="new-password"
                            />
                        </div>
                    </div>
                </div>
                </div>
                <?endif;?>

                <?if($delivery->isShopStores() && $delivery->isChecked()):
                    $shops = $delivery->getShops();
                ?>
                <div class="all-hide-radio">
                <div class="radio-hide" style="display: block;">
                    <div class="radio-hide-select">
                        <div class="one-order-tit">
                            Магазины
                        </div>
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
                <?endif;?>

            <?endforeach;?>

            </div>

        </div>
        <!-- Конец Способ доставки -->

        <div class="bt-order">
            <button class="border-black" href="javascript:App.checkout.submit();">
                    <?if($order->getPaySystemId() == 3) echo i18n('TO_PAYMENT', 'order');
                        else echo i18n('TO_CHECKOUT', 'order');
                    ?>
            </button>
        </div>

    </div>
    <!-- Конец Правая часть -->
    </form>

    <script type="text/javascript">
        $(document).ready(function(){
            App.CheckoutWidget.lang = '<?=i18n()->lang()?>';
            App.checkout = new App.CheckoutWidget($('#sale_order'), {
                isAuth: <?=intval($isUserAuth)?>,
                componentParams: '<?=$component->getSignedComponentParams()?>'
            });
            App.getPhoneSelect({
                object:$('.phone')
            });

            jQuery.each(jQuery('.one-order-form textarea'), function() {
                var offset = this.offsetHeight - this.clientHeight;

                var resizeTextarea = function(el) {
                    jQuery(el).css('height', 'auto').css('height', el.scrollHeight + offset);
                };
                jQuery(this).on('keyup input', function() { resizeTextarea(this); });
            });
            $('input[name="ORDER_PROP_3"]').on("blur", function () {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if(regex.test(this.value)) { try {rrApi.setEmail(this.value);}catch(e){}}
            });

            App.Gtm.GetOrderShow(<?=$arResult['BASKET_GTM']?>);
        });
        var google_tag_params = {
            dynx_itemid: [<?=implode(',', $arIds)?>],
            dynx_pagetype: "conversionintent",
            dynx_totalvalue: <?=$order->getPrice()?>
        };
    </script>
</div>
