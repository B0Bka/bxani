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

<section id="recomended" data-gtm="New">
<?if(count($products) > 0):?>
    <div class="section-tit">
ВАМ также могут понравиться
    </div>

    <div class="news-slider" id="catalog_products_list">
        <div class="container">
            <div class="owl-carousel recomended">
                <?foreach($products as $i => $product):?>
                    <?
                    $APPLICATION->IncludeComponent('aniart:blank', 'catalog.history.item', [
                        'IBLOCK_ID' => $product->getId(),
                        'CACHE_TYPE' => 'A',
                        'CACHE_TIME' => 36000,
                        'PRODUCT' => $product,
                    ], $component)
                    ?>
                <?endforeach;?>
            </div>
        </div>
    </div>
<?endif;?>
</section>
