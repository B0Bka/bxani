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
$cnt = $property->ValuesCount(true);
$sefController = $arParams['FILTER']->GetSEFController();
$labelId = 'one_filter_' . $property->GetID();
$isColorProp = $property->GetParam('CODE') == 'COLOR';
$isCollectionProp = $property->GetParam('CODE') == 'SEASON';
$isParentSelected = $property->IsValueSelected($property->GetID());
$isFilterOpened = $property->getPropertyOpen();
?>

<? if ($cnt > 0): ?>
    <li class="one_filter <?=$labelId?> <?=($isFilterOpened) ? "opened":""?>"
        data-filter_group_code="<?=strtolower($property->GetData('CODE'))?>">
        <span class="toggle-open-js"></span>
        <a href="javascript:void(0);">
            <?=i18n($property->GetParam("CODE"), 'filter') ?>:
            <span class="mob-selected-values-append"></span>
            <i class="fa fa-angle-down" aria-hidden="true"></i>
        </a>
        <ul class="filter-group
        <?= $isColorProp ? 'filt-color' : '' ?>
        <?=$isCollectionProp ? 'collection' : ''?>
        <?=($isFilterOpened) ? "filt-opened":""?>
        ">
            <? foreach ($property->GetValues() as $value):
                $isSelected = $property->IsValueSelected($value['ID']);
                $sefUrl = $sefController->getPropertyLink($property->GetID(), $value['ID']);
                $disabled = (!$isSelected && !$value['COUNT']?'disabled':'');
                ?>

                <li class="<?=$disabled?> one-filt-element">
                    <label class="<?= ($isSelected ? 'checked' : '') ?> <?=$disabled?>"
                    >
                        <input
                                type="checkbox"
                                data-url="<?= $sefUrl ?>"
                                data-code="<?= $value['CODE'] ? $value['CODE'] : $value['ID'] ?>"
                                data-propcode="<?= strtolower($property->GetData('CODE')) ?>"
                                value="<?= $value['ID'] ?>"
                                id="label_<?= $property->GetID() ?>_<?= $value['ID'] ?>"
                                data-property_id="<?=$property->GetID()?>"
                                data-section_name="<?=i18n($property->GetParam("CODE"), 'filter') ?>"
                                data-filter_name="<?=$value['NAME']?>"
                                onchange="App.CatalogFilter.submitFilter(this)"
                            <?=$disabled?>
                            <?= ($isSelected ? 'checked' : '') ?>
                        >
	                    <? if ($isColorProp): ?>
                            <span style="background-image:url(<?= $value['FILE'] ?>);" data-toggle="tooltip"
                                  data-placement="bottom" title="<?= $value['NAME'] ?>"></span>
	                    <? else: ?>
                            <span><?= $value['NAME'] ?></span>
	                    <? endif; ?>
                    </label>
                </li>
            <? endforeach; ?>
        </ul>
    </li>
<? endif; ?>
