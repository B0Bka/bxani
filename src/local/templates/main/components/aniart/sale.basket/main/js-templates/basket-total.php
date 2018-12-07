<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
	<div class="basket-checkout-container" data-entity="basket-checkout-aligner">
        <div class="basket-checkout-section" id="basket-checkout-section-title">
			<div class="basket-checkout-section-inner">
				<div class="basket-checkout-block basket-checkout-block-title">
					<div class="basket-checkout-block-total-inner">
                        <h4><?=i18n('YOUR_ORDER', 'order')?></h4>
                    </div>
                </div>
            </div>
        </div>
		<div class="basket-checkout-section">
			<div class="basket-checkout-section-inner">
				<div class="basket-checkout-block basket-checkout-block-total-price">
					<div class="basket-checkout-block-total-price-inner">
							<div class="sum-row">
								<div class="sum-price-name"><?=i18n('BASKET_SUM', 'order')?>: </div>
                                <div class="sum-price-num">{{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}</div>
							</div>
                        {{#DISCOUNT_PRICE_FORMATED}}
                            <div class="sum-row">
								<div class="sum-price-name"><?=i18n('SUMM_DISCOUNT', 'order')?>: </div>
                                <div class="sum-price-num">{{{DISCOUNT_PRICE_FORMATED}}}</div>
							</div>
						{{/DISCOUNT_PRICE_FORMATED}}
                        <div class="sum-row">
                            <div class="sum-price-name"><?=i18n('DELIVERY', 'order')?>: </div>
                            <div class="sum-price-num">{{{DELIVERY_PRICE}}}</div>
                        </div>
                    </div>
                    <div class="sum-row-final">
                        <div class="sum-price-name"><?=i18n('TOTAL', 'order')?>: </div>
                        <div class="sum-price-num" data-entity="basket-total-price">{{{PRICE_FORMATED}}}</div>
                    </div>
                    <div class="basket-checkout-block basket-checkout-block-btn">
                        <button class="btn btn-lg btn-primary basket-btn-checkout{{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}"
                            data-entity="basket-checkout-button" id="basket-button" data-page="<?=!CUser::IsAuthorized() ? 'signin' : 'order'?>">
                            <?=i18n('TO_ORDER', 'order')?>
                        </button>
                    </div>
                    <div class="basket-checkout-block basket-checkout-block-btn show-mobile">
                        <a href="/catalog/">
                            <button class="btn btn-lg btn-primary basket-btn-checkout btn-return" id="return-catalog-button">
                                <?=i18n('TO_CATALOG', 'order')?>
                            </button>
                        </a>
                    </div>
                    <?
                    if ($arParams['HIDE_COUPON'] !== 'Y')
                    {
                        ?>
                        <div class="basket-coupon-section">
                            <div class="basket-coupon-block-field">
                                <div class="basket-coupon-block-field-description" onclick="BX.Sale.BasketComponent.showCoupon(this)">
                                    <?=i18n('PROMO_CODE', 'order')?>
                                    <span class="show-promo">+</span>
                                </div>
                                <div class="form" id="coupon-group" style="display: {{SHOW_COUPON}}">
                                    <div class="form-group" style="position: relative;">
                                        <input type="text" class="form-control" id="" placeholder="" data-entity="basket-coupon-input">
                                        <span class="basket-coupon-block-coupon-btn">OK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?
                    }
                    ?>
				</div>
			</div>
		</div>

		<?
		if ($arParams['HIDE_COUPON'] !== 'Y')
		{
		?>
			<div class="basket-coupon-alert-section">
				<div class="basket-coupon-alert-inner">
					{{#COUPON_LIST}}
					<div class="basket-coupon-alert text-{{CLASS}}">
						<span class="basket-coupon-text">
							<strong>{{COUPON}}</strong> - <?=Loc::getMessage('SBB_COUPON')?> {{JS_CHECK_CODE}}
							{{#DISCOUNT_NAME}}({{{DISCOUNT_NAME}}}){{/DISCOUNT_NAME}}
						</span>
						<span class="close-link" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
							<?=Loc::getMessage('SBB_DELETE')?>
						</span>
					</div>
					{{/COUPON_LIST}}
				</div>
			</div>
			<?
		}
		?>
	</div>
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
</script>