<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @var array $arParams
 * @var CustomFilterProperty $property
 * @var \Aniart\Main\Seo\CustomFilterSEFController $sefController
 */
$property = $arParams["PROPERTY"];
$cnt = $property->ValuesCount(true);
$sefController = $arParams['FILTER']->GetSEFController();
$isColorProp = $property->GetParam('CODE') == 'COLOR';
$labelId = 'one_filter_'.$property->GetID();
?>

<?if($cnt > 0):?>
	<?if(count($property->GetSelectedValues()) > 1){
		$top_filter_text = i18n($property->GetParam('TITLE'), 'filter') . ' '
			. $property->GetValue($property->GetSelectedValues()[0])["NAME"] . ' + ' . (count($property->GetSelectedValues()) - 1);
	} else if(count($property->GetSelectedValues()) == 1){
		$top_filter_text = i18n($property->GetParam('TITLE'), 'filter') . ' '
			. $property->GetValue($property->GetSelectedValues()[0])["NAME"];
	};
	if (count($property->GetSelectedValues()) > 0){
		$selectedFilterClass = 'selected_values';
	}
	?>
<div class="multi-sel">
    <div class="multi-tit <?=$selectedFilterClass?>">
        <?=(isset($top_filter_text)) ? $top_filter_text : i18n($property->GetParam('TITLE'), 'filter')?>
    </div>
    <ul  class="<?= $isColorProp ? 'filt-color' : '' ?>">
    <?foreach($property->GetValues() as $value):
        $isSelected = $property->IsValueSelected($value['ID']);
        $sefUrl = $sefController->getPropertyLink($property->GetID(), $value['ID']);
        $disabled = (!$isSelected && !$value['COUNT']?'disabled':'');
    ?>
        <li class="<?=$disabled?> one-filt-element">
            <label class="<?=($isSelected ? 'checked' : '')?> <?=$disabled?>">
                <input
                    type="checkbox"
                    data-url="<?=$sefUrl?>"
                    data-code="<?=$value['CODE'] ? $value['CODE'] : $value['ID']?>"
                    data-propcode="<?=strtolower($property->GetData('CODE'))?>"
                    value="<?=$value['ID']?>"
                    id="label_<?=$property->GetID()?>_<?=$value['ID']?>"
                    data-property_id="<?=$property->GetID()?>"
                    onchange="App.CatalogFilter.submitFilter(this)"
                    data-section_name="<?=i18n($property->GetParam("CODE"), 'filter') ?>"
                    data-filter_name="<?=$value['NAME']?>"
                    <?=$disabled?> 
                    <?=($isSelected?'checked':'')?> 
                >
                <? if ($isColorProp): ?>
                    <span style="background-image:url(<?= $value['FILE'] ?>);" data-toggle="tooltip"
                          data-placement="bottom" title="<?= $value['NAME'] ?>"></span>
                <? else: ?>
                    <span><?= $value['NAME'] ?></span>
                <? endif; ?>

            </label>
        </li>
    <?endforeach;?>
    </ul>
</div>

<?endif;?>

