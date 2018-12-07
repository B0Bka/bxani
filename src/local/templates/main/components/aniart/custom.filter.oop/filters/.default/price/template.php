<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
/**
 * @var array $arParams
 * @var CustomFilterProperty $property
 * @var \Aniart\Main\Seo\CustomFilterSEFController $sefController
 */
$property = $arParams["PROPERTY"];
$cnt = $property->ValuesCount();
$sefController = $arParams['FILTER']->GetSEFController();
$labelId = 'one_filter_' . $property->GetID();
$isColorProp = $property->GetParam('CODE') == 'COLOR';
$isCollectionProp = $property->GetParam('CODE') == 'SEASON';
$isParentSelected = $property->IsValueSelected($property->GetID());
$isFilterOpened = $property->getPropertyOpen();
?>

<?if($labelId == "one_filter_".PROP_MIN_PRICE_ID){//цена?>
	<?
//        p($property->getPriceRanges());
	$minValue = $property->GetValue('min')["VALUE"];
	$maxValue = $property->GetValue('max')["VALUE"];

	$range = $maxValue - $minValue;
	?>
	<li class="one_filter <?=$labelId?> openfix <?=(app()->getDeviceType() == "mobile") ? "" : "opened"?> is-price-filter"
        style="order: 1"
        data-filter_group_code = "<?=strtolower($property->GetData('CODE'))?>">
		<span class="toggle-open-js"></span>
		<a href="javascript:void(0);">
			Цена:
            <span class="mob-selected-values-append"></span>
            <i class="fa fa-angle-down" aria-hidden="true"></i>
		</a>
		<ul class="checkbox-prices filter-group <?=(app()->getDeviceType() == "mobile") ? "" : "filt-opened"?>">
			<?foreach ($property->getPriceRanges() as $key=>$value){?>
				<li>
					<label class="range-set">
						<input type="checkbox" name="price-range" data-min="<?=$value['min']?>" data-max="<?=$value['max']?>">
						<?=$value["text"]?>
					</label>
				</li>
			<?}?>
			<li class="filter-control">
				<input type="hidden" name="default-min-price" value="<?=$minValue?>">
				<input type="hidden" name="default-max-price" value="<?=$maxValue?>">
				<div id="range-filter" type="text" class="span2" value="" data-slider_min="<?=$minValue?>" data-slider_max="<?=$maxValue?>" data-slider-step="5"></div>
				<div class="range-input-text-area">
					<input type="text" value="<?=$minValue?>" name="text-min-value" id="text-min-value" data-property_id="<?=$property->GetID()?>">
					<input type="text" value="<?=$maxValue?>" name="text-max-value" id="text-max-value" data-property_id="<?=$property->GetID()?>">
				</div>
				<div id="submit-price-filter">применить</div>
				<div id="reset-price-filter">отменить</div>
			</li>
		</ul>

	</li>
<?}?>
