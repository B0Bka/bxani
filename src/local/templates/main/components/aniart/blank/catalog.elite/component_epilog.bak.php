<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$siblings = [];
$product = $arParams['PRODUCT'];

//new \dBug($sibling);
//new \dBug($arResult['PAGINATION']);

foreach($product->getSibling() as $item)
{
    $images = [];
    $imgData = $item->getMorePhotoElite(2, 590, 420);
    if($imgData)
    {
        foreach($imgData as $img)
            $images[] = $img['src'];
    }
    $offers = $item->getOffers();
    $sizes = [];
    foreach($offers as $offer)
    {
        $sizes[] = [
            'offerId'=>$offer->getId(),
            'value'=>$offer->getSize()
        ];
    }
    $siblings[$item->getId()] = [
        'id'=>$item->getId(),
        'name'=>$item->getName(),
        'url'=>$item->getDetailPageUrl(),
        'img'=>$images,
        'sizes'=>$sizes,
        'color'=>$item->getClothData(),
        'priceDiscount'=>$item->getDiscountPrice(true),
        'price'=>$item->getBasePrice(true),
        'isDiscount'=>$item->getDiscountPrice()
    ];
}
?>

<script type="text/javascript">
$(document).ready(function(){
    App.Catalog.setSiblingElite(<?=CUtil::PhpToJSObject($siblings)?>);
});
</script>
