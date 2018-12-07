<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @var array $arParams
 * @var CustomFilterProperty $property
 * @var \Aniart\Main\Seo\CustomFilterSEFController $sefController
 */
$property = $arParams["PROPERTY"];
$cnt = $property->ValuesCount();
$sefController = $arParams['FILTER']->GetSEFController();

$labelId = 'one_filter_'.$property->GetID();
?>

<?if($cnt > 0):?>
<div class="multi-sel">
    <div class="multi-tit">
        <?=i18n($property->GetParam('TITLE'), 'filter')?> 
    </div>
    <ul class="filt-color">
    <?foreach($property->GetValues() as $value):
        $isSelected = $property->IsValueSelected($value['ID']);
        $sefUrl = $sefController->getPropertyLink($property->GetID(), $value['ID']);
        $disabled = (!$isSelected && !$value['COUNT']?'disabled':'');
    ?>
        <li>
            <label 
                for="label_<?=$property->GetID()?>_<?=$value['ID']?>" 
                class="<?=($isSelected?'checked':'')?> <?=$disabled?>"
            >
                <input 
                    type="checkbox" 
                    data-url="<?=$sefUrl?>"
                    data-code="<?=$value['CODE'] ? $value['CODE'] : $value['ID']?>"
                    data-propcode="<?=strtolower($property->GetData('CODE'))?>"
                    value="<?=$value['ID']?>"
                    id="label_<?=$property->GetID()?>_<?=$value['ID']?>"
                    onchange="App.CatalogFilter.submitFilter(this)"
                    <?=$disabled?> 
                    <?=($isSelected?'checked':'')?> 
                >
                <span
                        style="background-image:url(<?=$value['FILE']?>)"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="<?=$value['NAME']?>"
                ></span>
            </label>
        </li>
    <?endforeach;?>
    </ul>
</div>
<?endif;?>