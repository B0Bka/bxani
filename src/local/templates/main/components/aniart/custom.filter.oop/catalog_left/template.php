<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$filter = $arResult["FILTER"];
$selectedValues = $filter->GetSelectedValues();
/**
 * @var \Aniart\Main\Seo\CustomFilterSEFController $sefController
 */
$sefController = $filter->GetSEFController();
$sectionQueryUrl = getLinkWithQueryString($arParams['REQUEST_PAGE_URL']);

$selectedValuesCount = $selectedValues->SelectedAllValuesCount();
?>
<div class="cat-filter-left" id="cat-filter-left">

<? if ($filter->PropertiesCount() > 0): ?>
    <? foreach ($filter->GetProperties() as $obProperty): ?>
        <?= $obProperty->GetHtml('select'); ?>
    <? endforeach ?>
<? endif ?>

</div>
<script>
    <?
    $jsFilter = CUtil::PhpToJSObject($arParams['MORE_PROPERTY']);
    ?>
    (function () {
        var $filter = $('.cat-filter-left');
        App.CatalogLeftFilter = new App.CustomLeftFilter($filter, {
            url: '<?=$arParams['REQUEST_PAGE_URL']?>',
            filter: <?=$jsFilter?>,
            lang: '<?=i18n()->lang()?>',
            $properties: $filter.find('#cat-filter-left'),

        });
        App.CatalogLeftFilter.onClickItemEvent($filter);

    })();
</script>
