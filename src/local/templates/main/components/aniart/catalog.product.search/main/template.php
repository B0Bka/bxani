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

//new \dBug($component);
//new \dBug($arResult['PAGINATION']);
?>


<?foreach($products as $i => $product):
    $pos = ++$i;
    $isElite = ($pos == 3) || ($pos == 8);
    ?>

    <?
    $APPLICATION->IncludeComponent('aniart:blank', $isElite ? 'catalog.elite' : 'catalog.item', [
        'IBLOCK_ID' => $product->getId(),
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => 36000,
        'PRODUCT' => $product,
    ], $component)
    ?>
<?endforeach;?>
