<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var AniartProductsListComponent $component
 * @var \Aniart\Main\Models\Product[] $products
 */
$component = $this->getComponent();
$products = $arResult['PRODUCTS'];
//dBug($arResult);

?>

<div class="my-fav">
    <? if (count($products) <= 0): ?>
        <?=i18n("EmptyWishList")?>
    <? else: ?>
        <? foreach ($products as $i => $product):?>

            <?
            $APPLICATION->IncludeComponent('aniart:blank',
                'catalog.wish.item',
                [
                'IBLOCK_ID' => $product->getId(),
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => 36000,
                'PRODUCT' => $product,
                ],
            $component)
            ?>
        <? endforeach; ?>
    <? endif; ?>
</div>

