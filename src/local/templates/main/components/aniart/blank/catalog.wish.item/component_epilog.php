<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$siblings = [];
$product = $arParams['PRODUCT'];

//new \dBug($product->getSibling());
//new \dBug($arResult['PAGINATION']);

foreach($product->getSibling() as $item)
{
    $images = SITE_TEMPLATE_PATH.'/images/no_photo.png';
//    $imgData = $item->getAllImagesId(1);
    if($imgData)
    {
        foreach($imgData as $img)
            $images = $item->getFilePath($img);
    }
//    $offers = $item->getOffers();
    $sizes = [];
//    foreach($offers as $offer)
//    {
//        $sizes[] = [
//            'offerId'=>$offer->getId(),
//            'value'=>$offer->getSize()
//        ];
//    }
//    $siblings[$item->getId()] = [
//        'id'=>$item->getId(),
//        'name'=>$item->getName(),
//        'url'=>$item->getDetailPageUrl(),
//        'img'=>$images,
//        'sizes'=>$sizes,
//        'color'=>$item->getClothData(),
//        'priceDiscount'=>$item->getDiscountPrice(true),
//        'price'=>$item->getBasePrice(true),
//        'isDiscount'=>$item->getDiscountPrice()
//    ];
}
?>

<script type="text/javascript">
$(document).ready(function(){
    App.Catalog.setSibling(<?=CUtil::PhpToJSObject($siblings)?>);
});
</script>
