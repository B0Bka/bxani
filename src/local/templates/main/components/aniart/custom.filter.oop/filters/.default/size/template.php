<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$property = $arParams["PROPERTY"];
$cnt = $property->ValuesCount();
$sefController = $arParams['FILTER']->GetSEFController();

$labelId = 'one_filter_' . $property->GetID();
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
        <?if(!empty($arParams['SIZES'])):?>
            <div class="size-set-filter">
                <?foreach ($arParams['SIZES'] as $key => $code):
                    $selected = '';
                    if((empty($_COOKIE['sizeType']) && $key == 0) || $_COOKIE['sizeType'] == $code)
                        $selected = 'class="selected"';
                    ?>
                    <span
                            <?=$selected?>
                            data-code="<?=$code?>"
                            onclick="App.CatalogFilter.changeSizeType('<?=$code?>', this);"
                    >
                        <?=$code?>
                    </span>
                <?endforeach;?>
            </div>
        <?endif;?>
        <ul class="filter-group
            <?=($isFilterOpened) ? "filt-opened":""?>
            ">

            <? foreach ($property->GetValues() as $value):
                $isSelected = $property->IsValueSelected($value['ID']);
                $sefUrl = $sefController->getPropertyLink($property->GetID(), $value['ID']);
                $disabled = (!$isSelected && !$value['COUNT']?'disabled':'');
                if(!empty($_COOKIE['sizeType']) && !empty($arParams['NAMES'][$value['ID']][$_COOKIE['sizeType']]))
                    $name = $arParams['NAMES'][$value['ID']][$_COOKIE['sizeType']];
                else
                    $name = $value['NAME'];
                ?>

                <li class="<?=$disabled?>" onclick="App.CatalogFilter.submitFilter()" >
                    <label for="label_<?= $property->GetID() ?>_<?= $value['ID'] ?>"
                           class="<?= ($isSelected ? 'checked' : '') ?> <?=$disabled?>">
                        <span
                                class="size-prop"
                                data-name-eu="<?= $value['NAME'] ?>"
                                data-name-ua="<?= $arParams['NAMES'][$value['ID']]['ua'] ?>"
                                data-name-int="<?= $arParams['NAMES'][$value['ID']]['int'] ?>"
                        >
                            <?= $name ?>
                        </span>
                        <input
                                type="checkbox"
                                data-url="<?= $sefUrl ?>"
                                data-code="<?= $value['CODE'] ? $value['CODE'] : $value['ID'] ?>"
                                data-propcode="<?= strtolower($property->GetData('CODE')) ?>"
                                value="<?= $value['ID'] ?>"
                                id="label_<?= $property->GetID() ?>_<?= $value['ID'] ?>"
                            <?=$disabled?> 
                            <?= ($isSelected ? 'checked' : '') ?>
                        >
                    </label>
                </li>
            <? endforeach; ?>
        </ul>
    </li>
<? endif; ?>