<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $mobileColumns
 * @var array $arParams
 * @var string $templateFolder
 */

$usePriceInAdditionalColumn = in_array('PRICE', $arParams['COLUMNS_LIST']) && $arParams['PRICE_DISPLAY_MODE'] === 'Y';
$useSumColumn = in_array('SUM', $arParams['COLUMNS_LIST']);
$useActionColumn = in_array('DELETE', $arParams['COLUMNS_LIST']);

$restoreColSpan = 3 + $usePriceInAdditionalColumn + $useSumColumn + $useActionColumn;

$positionClassMap = array(
	'left' => 'basket-item-label-left',
	'center' => 'basket-item-label-center',
	'right' => 'basket-item-label-right',
	'bottom' => 'basket-item-label-bottom',
	'middle' => 'basket-item-label-middle',
	'top' => 'basket-item-label-top'
);

$discountPositionClass = '';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
	{
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$labelPositionClass = '';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
	{
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}
?>

<script id="basket-item-template" type="text/html">
	<tr class="basket-items-list-item-container{{#SHOW_RESTORE}} basket-items-list-item-container-expend{{/SHOW_RESTORE}}"
		id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">
		{{#SHOW_RESTORE}}
			<td class="basket-items-list-item-notification" colspan="<?=$restoreColSpan?>">
				<div class="basket-items-list-item-notification-inner basket-items-list-item-notification-removed" id="basket-item-height-aligner-{{ID}}">
					{{#SHOW_LOADING}}
						<div class="basket-items-list-item-overlay"></div>
					{{/SHOW_LOADING}}
					<div class="basket-items-list-item-removed-container">
						<div>
							<?=Loc::getMessage('SBB_GOOD_CAP')?> <strong>{{NAME}}</strong> <?=Loc::getMessage('SBB_BASKET_ITEM_DELETED')?>.
						</div>
						<div class="basket-items-list-item-removed-block">
							<a href="javascript:void(0)" data-entity="basket-item-restore-button">
								<?=Loc::getMessage('SBB_BASKET_ITEM_RESTORE')?>
							</a>
							<span class="basket-items-list-item-clear-btn" data-entity="basket-item-close-restore-button"></span>
						</div>
					</div>
				</div>
			</td>
		{{/SHOW_RESTORE}}
		{{^SHOW_RESTORE}}
            <td class="basket-items-list-item-color basket-items-list-item-mobile">
                <div class="one-your-order basket-product step-order">
                    <div class="one-your-order-thumb">
                        <img src="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?=$templateFolder?>/images/no_photo.png{{/IMAGE_URL}}" alt="{{NAME}}" title="{{NAME}}">
                        {{#HAS_DISCOUNT}}
                                <div class="action-mark">%</div>
                        {{/HAS_DISCOUNT}}
                    </div>
                    <div class="one-your-order-descr">
                        <a href="{{DETAIL_PAGE_URL}}" class="one-your-order-item-name">{{NAME}}</a>
                        <span class="one-your-order-item-article"><?=i18n('ARTICLE')?>: {{ARTICLE}}</span>
                    </div>
                    <div class="one-your-order-info-basket">
                        <div class="one-your-order-item-param">
                            Цвет: Голубой
                        </div>
                        <div class="one-your-order-item-param">
                            Размер:
                                {{#SKU_BLOCK_LIST}}
                                    {{^IS_IMAGE}}
                                            {{#SKU_VALUES_LIST}}
                                                {{#SELECTED}}{{NAME}}{{/SELECTED}}
                                            {{/SKU_VALUES_LIST}}
                                    {{/IS_IMAGE}}
                                {{/SKU_BLOCK_LIST}}
                        </div>
                        <div class="one-your-order-item-param">
                            Кол-во:
                                {{#QUANTITY_LIST}}
                                    {{#SELECTED}}{{VALUE}}{{/SELECTED}}
                                {{/QUANTITY_LIST}}
                        </div>
                        <div class="one-your-order-item-param">
                            Цена: {{{PRICE_FORMATED}}}
                            {{#SHOW_DISCOUNT_PRICE}}
                                <span class="one-your-order-item-discount-price">{{{FULL_PRICE_FORMATED}}}</span>
						    {{/SHOW_DISCOUNT_PRICE}}

                        </div>
                    </div>
            </td>
			<td class="basket-items-list-item-descriptions basket-items-list-item-desktop">
                <div class="header-fix-tablet">Наименование</div>
				<div class="basket-items-list-item-descriptions-inner" id="basket-item-height-aligner-{{ID}}">
					<?
					if (in_array('PREVIEW_PICTURE', $arParams['COLUMNS_LIST']))
					{
						?>
						<div class="basket-item-block-image<?=(!isset($mobileColumns['PREVIEW_PICTURE']) ? ' d-none d-sm-block' : '')?>">
							{{#DETAIL_PAGE_URL}}
								<a href="{{DETAIL_PAGE_URL}}" class="basket-item-image-link">
							{{/DETAIL_PAGE_URL}}

							<img class="basket-item-image" alt="{{NAME}}"
								src="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?=$templateFolder?>/images/no_photo.png{{/IMAGE_URL}}">

							{{#SHOW_LABEL}}
								<div class="basket-item-label-text basket-item-label-big <?=$labelPositionClass?>">
									{{#LABEL_VALUES}}
										<div{{#HIDE_MOBILE}} class="d-none d-sm-block"{{/HIDE_MOBILE}}>
											<span title="{{NAME}}">{{NAME}}</span>
										</div>
									{{/LABEL_VALUES}}
								</div>
							{{/SHOW_LABEL}}

                            {{#HAS_DISCOUNT}}
                                <div class="action-mark">%</div>
                            {{/HAS_DISCOUNT}}
							<?
							if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y')
							{
								?>
								{{#DISCOUNT_PRICE_PERCENT}}
									<div class="basket-item-label-ring basket-item-label-small <?=$discountPositionClass?>">
										-{{DISCOUNT_PRICE_PERCENT_FORMATED}}
									</div>
								{{/DISCOUNT_PRICE_PERCENT}}
								<?
							}
							?>

							{{#DETAIL_PAGE_URL}}
								</a>
							{{/DETAIL_PAGE_URL}}
						</div>
						<?
					}
					?>
					<div class="basket-item-block-info">
						<h2 class="basket-item-info-name">
							{{#DETAIL_PAGE_URL}}
								<a href="{{DETAIL_PAGE_URL}}" class="basket-item-info-name-link">
							{{/DETAIL_PAGE_URL}}
	
							<span data-entity="basket-item-name">{{NAME}}</span>

							{{#DETAIL_PAGE_URL}}
								</a>
							{{/DETAIL_PAGE_URL}}
						</h2>
						{{#NOT_AVAILABLE}}
							<div class="basket-items-list-item-warning-container">
								<div class="alert alert-warning text-center">
									<?=Loc::getMessage('SBB_BASKET_ITEM_NOT_AVAILABLE')?>.
								</div>
							</div>
						{{/NOT_AVAILABLE}}
						{{#DELAYED}}
							<div class="basket-items-list-item-warning-container">
								<div class="alert alert-warning text-center">
									<?=Loc::getMessage('SBB_BASKET_ITEM_DELAYED')?>.
									<a href="javascript:void(0)" data-entity="basket-item-remove-delayed">
										<?=Loc::getMessage('SBB_BASKET_ITEM_REMOVE_DELAYED')?>
									</a>
								</div>
							</div>
						{{/DELAYED}}
						{{#WARNINGS.length}}
							<div class="basket-items-list-item-warning-container">
								<div class="alert alert-warning alert-dismissable" data-entity="basket-item-warning-node">
									<span class="close" data-entity="basket-item-warning-close">&times;</span>
										{{#WARNINGS}}
											<div data-entity="basket-item-warning-text">{{{.}}}</div>
										{{/WARNINGS}}
								</div>
							</div>
						{{/WARNINGS.length}}
						<div class="basket-item-block-properties">
                            <?=i18n('ARTICLE')?>: {{ARTICLE}}
						</div>
					</div>

					{{#SHOW_LOADING}}
						<div class="basket-items-list-item-overlay"></div>
					{{/SHOW_LOADING}}
				</div>
			</td>
            <td class="basket-items-list-item-color basket-items-list-item-desktop">
                <div class="header-fix-tablet">Цвет</div>
				<div class="basket-item-block-color{{#NOT_AVAILABLE}} disabled{{/NOT_AVAILABLE}}"
					data-entity="basket-item-color-block">
                        <span
                            style="background-image:url({{COLOR}});"
                            data-toggle="tooltip"
                            data-placement="bottom"
                            class="basket-item-color"
                        ></span>
					{{#SHOW_LOADING}}
						<div class="basket-items-list-item-overlay"></div>
					{{/SHOW_LOADING}}
				</div>
			</td>

            <td class="basket-items-list-item-sku basket-items-list-item-desktop"">
                <div class="header-fix-tablet">Размер</div>
				<div class="basket-item-block-sku{{#NOT_AVAILABLE}} disabled{{/NOT_AVAILABLE}}"
					data-entity="basket-item-sku-block">
                        {{#SKU_BLOCK_LIST}}
                            {{^IS_IMAGE}}
                                <select data-entity="basket-item-sku-select" onchange="BX.Sale.BasketComponent.changeSkuSelect(this.value, this)">
                                    {{#SKU_VALUES_LIST}}
                                        <option value="{{VALUE_ID}}" {{#SELECTED}}selected class="selected"{{/SELECTED}}>
                                            {{NAME}}
                                        </option>
                                    {{/SKU_VALUES_LIST}}
                                </select>
                            {{/IS_IMAGE}}
                        {{/SKU_BLOCK_LIST}}
					{{#SHOW_LOADING}}
						<div class="basket-items-list-item-overlay"></div>
					{{/SHOW_LOADING}}
				</div>
			</td>

            <td class="basket-items-list-item-amount basket-items-list-item-desktop"">
                <div class="header-fix-tablet">Кол-во</div>
				<div class="basket-item-block-amount{{#NOT_AVAILABLE}} disabled{{/NOT_AVAILABLE}}"
					data-entity="basket-item-quantity-block">
					<div class="basket-item-amount-filed-block">

                            <select data-entity="basket-item-sku-select" onchange="BX.Sale.BasketComponent.changeQuantitySelect(this.value, this)">
                                {{#QUANTITY_LIST}}
                                    <option value="{{VALUE}}" {{#SELECTED}}selected class="selected"{{/SELECTED}}>
                                        {{VALUE}}
                                    </option>
                                {{/QUANTITY_LIST}}
                            </select>
					</div>
					{{#SHOW_LOADING}}
						<div class="basket-items-list-item-overlay"></div>
					{{/SHOW_LOADING}}
				</div>
			</td>
			<?
			if ($usePriceInAdditionalColumn)
			{
				?>
				<td class="basket-items-list-item-price basket-items-list-item-desktop" basket-items-list-item-price-for-one<?=(!isset($mobileColumns['PRICE']) ? ' d-none d-sm-block' : '')?>">
                    <div class="header-fix-tablet">Стоимость</div>
					<div class="basket-item-block-price">
						<div class="basket-item-price-current">
							<span class="basket-item-price-current-text" id="basket-item-price-{{ID}}">
								{{{PRICE_FORMATED}}}
							</span>
						</div>

                        {{#SHOW_DISCOUNT_PRICE}}
							<div class="basket-item-price-old">
								<span class="basket-item-price-old-text">
									{{{FULL_PRICE_FORMATED}}}
								</span>
							</div>
						{{/SHOW_DISCOUNT_PRICE}}

						{{#SHOW_LOADING}}
							<div class="basket-items-list-item-overlay"></div>
						{{/SHOW_LOADING}}
					</div>
				</td>
				<?
			}
			?>

			<?
			if ($useSumColumn)
			{
				?>
				<td class="basket-items-list-item-price basket-items-list-item-desktop"<?=(!isset($mobileColumns['SUM']) ? ' d-none d-sm-block' : '')?>">
                    <div class="header-fix-tablet">Сумма</div>
					<div class="basket-item-block-price">
						{{#SHOW_DISCOUNT_SUM}}
							<div class="basket-item-price-old">
								<span class="basket-item-price-old-text" id="basket-item-sum-price-old-{{ID}}">
									{{{SUM_FULL_PRICE_FORMATED}}}
								</span>
							</div>
						{{/SHOW_DISCOUNT_SUM}}

						<div class="basket-item-price-current">
							<span class="basket-item-price-current-text" id="basket-item-sum-price-{{ID}}">
								{{{SUM_PRICE_FORMATED}}}
							</span>
						</div>

						{{#SHOW_DISCOUNT_SUM}}
							<div class="basket-item-price-difference">
								<?=Loc::getMessage('SBB_BASKET_ITEM_ECONOMY')?>
								<span id="basket-item-sum-price-difference-{{ID}}" style="white-space: nowrap;">
									{{{SUM_DISCOUNT_PRICE_FORMATED}}}
								</span>
							</div>
						{{/SHOW_DISCOUNT_SUM}}
						{{#SHOW_LOADING}}
							<div class="basket-items-list-item-overlay"></div>
						{{/SHOW_LOADING}}
					</div>
				</td>
				<?
			}

			if ($useActionColumn)
			{
				?>
				<td class="basket-items-list-item-remove d-none d-sm-block">
					<div class="basket-item-block-actions">
						<span class="basket-item-actions-remove" data-entity="basket-item-delete"></span>
						{{#SHOW_LOADING}}
							<div class="basket-items-list-item-overlay"></div>
						{{/SHOW_LOADING}}
					</div>
				</td>
				<?
			}
			?>
		{{/SHOW_RESTORE}}
	</tr>
</script>