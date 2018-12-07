<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var AniartProductsListComponent $component
 * @var \Aniart\Main\Models\Product[] $products
 */
$component = $this->getComponent();
$products  = $arResult['PRODUCTS'];
$pos = 1;
?>
<a
        class="anchor"
        id = "page-<?=$arResult['PAGINATION']["NavPageNomer"]?>"
        name="page-<?=$arResult['PAGINATION']["NavPageNomer"]?>"
        data-page = "<?=$arResult['PAGINATION']["NavPageNomer"]?>">

</a>
<?foreach($products as $i => $product):
    $isElite = ($pos == 3) || ($pos == 8);
    $APPLICATION->IncludeComponent('aniart:blank', $isElite ? 'catalog.elite' : 'catalog.item', [
        'IBLOCK_ID' => $product->getId(),
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => 36000,
        'PRODUCT' => $product,
    ], $component)
    ?>
<?
    $pos++;
    if($pos == 11) $pos = 1;
endforeach;?>
<?
if($arResult["PAGINATION"]["NavPageNomer"] == $arResult["PAGINATION"]["NavPageCount"]){?>
    <a class="anchor" name="page-<?=$arResult['PAGINATION']["NavPageNomer"]?>" data-page = "<?=$arResult['PAGINATION']["NavPageNomer"]?>"></a>
<?}
?>


