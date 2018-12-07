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

//dBug($products);
?>
<div id="novlety" class="full-width-mobile">
<section id="new" data-gtm="New">
<?if(count($products) > 0):?>
    <div class="section-tit">
       <?=!empty($arParams['TITLE']) ? $arParams['TITLE'] : i18n("New arrivals") ?>
    </div>

    <div class="news-slider" id="catalog_products_list">
        <div class="news-slider-in owl-carousel">
            <?foreach($products as $i => $product):?>
                <?
                $APPLICATION->IncludeComponent('aniart:blank', 'catalog.novlety.item', [
                    'IBLOCK_ID' => $product->getId(),
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 36000,
                    'PRODUCT' => $product,
                ], $component)
                ?>
            <?endforeach;?>
        </div>
    </div>
<?endif;?>
</section>
</div>