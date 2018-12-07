<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$currentOffer = $arResult['CURRENT_OFFER'];
//new \dBug($GLOBALS, '', true);
$detailtext = $arResult['PRODUCT']->getDetailText();
$care = $arResult['PRODUCT']->getCare();
$consist = $arResult['PRODUCT']->getConsist();
$product = $arResult['PRODUCT'];
$tags = $product->getTagsItems();

//для retail rocket
$offerIds = array_keys($arResult['OFFERS']);
$offerStr = implode(', ',$offerIds);
?>
<!-- COOKIE -->
<?

//$value = $product->getId();
//if (!isset($_COOKIE['PRODUCT_VIEW'])) {
//    setcookie("PRODUCT_VIEW", $value, time()+864000, '/');
//} else {
//    if(substr_count($_COOKIE['PRODUCT_VIEW'], ",") > 2){
//        $ar = explode(",", $_COOKIE["PRODUCT_VIEW"]);
//        $unique_viewed = array_unique($ar);
//        array_push($unique_viewed, $value);
//        if(count($unique_viewed) > 21){
//            array_shift($unique_viewed);
//        }
//        $comma_separated = implode(",", $unique_viewed);
//        setcookie("PRODUCT_VIEW", $comma_separated, time()+864000, '/');
//    } else {
//        setcookie("PRODUCT_VIEW", $_COOKIE['PRODUCT_VIEW'] . "," . $value, time()+864000, '/');
//    }
//
//
//}
?>
<?
$basketService = app('BasketService');
$basketProductIds = $basketService->getProductIds();
function setSizeblock($sizes, $basketProductsId, $available, $showSizesTypes)
{
    $result = "";

    if($available && $showSizesTypes)
    {
        $sizeRepository = app('SizesRepository');
        $arSizes = $sizeRepository->getSizes();
        $result .= '<ul class="size-set">';
        foreach ($arSizes as $key => $code)
        {
            $selected = '';
            if((empty($_COOKIE['sizeType']) && $key == 0) || $_COOKIE['sizeType'] == $code)
                $selected = 'class="selected"';
            $result .= '
            <li
                    '.$selected.'
                    data-code="'.$code.'"
            >
                '.$code.'
            </li>';
        }
        $result .= '</ul>
		<div class="size-info"><div class="size-info-pic"><span class=tooltiptext>'.i18n('SIZE_INFO').'</span></div></div>
		';
    }
    if(!empty($sizes))
        $result .= '<div class="size-list">';
    foreach($sizes as $size)
    {
        if(in_array($size['offer_id'], $basketProductsId)) $inBasket = "true";
            else $inBasket = "";

            if(!empty($_COOKIE['sizeType']) && !empty($size['name_'.$_COOKIE['sizeType']]))
                $name = $size['name_'.$_COOKIE['sizeType']];
            else
                $name = $size['name'];

            if(isset($_COOKIE['checkedSize'])) {
                if($name == $_COOKIE['checkedSize']){
                    $checkedSize = 'checked';
                } else {
                    $checkedSize = '';
                }
            }
            if(!empty($size['name'])) {
	            $result .= '<div class="one-color">
            <label
                    data-offer="' . $size['offer_id'] . '"
                    data-size="' . $size['name'] . '"
                    data-astrafit_id="' . $size['astrafit_id'] . '"
                    data-in_basket="' . $inBasket . '"
                    class="' . $checkedSize . '"
                    data-name_eu="' . $size['name'] . '"
                    data-name_ua="' . $size['name_ua'] . '"
                    data-name_us="' . $size['name_us'] . '"
            >
                <span>' . $name . '</span>
                <input type="radio" name="' . $size['product_id'] . '"/>
            </label>
        </div>';
            }
    }
    if(!empty($sizes))
        $result .= '</div>';
    
    return $result;
}
$OutPut= preg_replace(
    "/#SIZE_BLOCK#/is".BX_UTF_PCRE_MODIFIER,
    setSizeblock($product->getOffersSize(), $basketProductIds, $product->getAvailable(), $product->showSizeTypes()),
    $arResult["CACHED_TPL"]);
echo $OutPut;
?>


<div class="acc-soc">
    <div class="product-map-block">
        <div id="product_detail_availability_block" class="prod-availability-block">
            <?$APPLICATION->IncludeComponent('aniart:stores.product.list', 'main', [
                'PRODUCT_ID' => null,
                //'CITY_ID' => 1, //default city id
                'SORT' => ['AMOUNT' => 'DESC'],
                'PAGE_SIZE' => 20,
                'CACHE_TYPE' => 'N',
                'CACHE_TIME' => 3600
            ], false);?>
        </div>
        <div id="map-product"></div>
    </div>
    <div class="prod-info">
        <? if ($detailtext): ?>
            <div class="one-prod-info">
                <div class="one-prod-info-tit active">
                    <?= i18n('PRODUCT_DESCRIPTION') ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
                </div>
                <div class="one-prod-info-descr" style="display: block">
                    <?= $detailtext ?>
                </div>

            </div>
            <div class="one-prod-info">
                <div class="one-prod-info-tit">
                    Состав и уход <i class="fa fa-angle-down" aria-hidden="true"></i>
                </div>
                <div class="one-prod-info-descr">
                    <div class="consist"><?=$product->getClothColorName()?></div>
                </div>
            </div>

        <? endif; ?>
        <? if ($care): ?>
            <div class="one-prod-info">
                <div class="one-prod-info-tit">
                    <?= i18n('CARE') ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
                </div>
                <div class="one-prod-info-descr">
                    <?=htmlspecialchars_decode($care['TEXT']) ?>
                </div>
            </div>

        <? endif; ?>
        <? if ($consist): ?>
            <div class="one-prod-info">
                <div class="one-prod-info-tit">
                    <?= i18n('FACTORY_COMPOSITION') ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
                </div>
                <div class="one-prod-info-descr">
                    <?=htmlspecialchars_decode($consist['TEXT']) ?>
                </div>

            </div>
        <?endif;?>
            <div class="one-prod-info" id="reviews_scroll">
                <div class="one-prod-info-tit">
                    <?= i18n('REVIEWS') ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
                </div>
            </div>
    </div>
   <?
   $tit = $APPLICATION->GetTitle();
   $url = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
   $imgs = $product->getAllImagesId();
   $image = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"] . CFile::GetPath($imgs[0]);
   $link = "http://www.facebook.com/sharer.php?s=100&amp;p[title]=".$tit."&amp;p[url]=".$url."&amp;p[images][0]=".$image.
       "','sharer','toolbar=0,status=0,width=548,height=325";
   $APPLICATION->AddHeadString('<meta property="og:image" content="' . $image . '" />');
   ?>

    <div class="prod-soc">
<!--        <a href="https://www.facebook.com/sharer/sharer.php?u=--><?//=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].""?><!--" id = "fb-lnk">-->
            <a onClick="window.open('<?=$link?>');" href="javascript: void(0)">
            <div class="o-flow"></div>
            <svg width="43" height="43" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42 42">
                <defs>
                    <style>.cls-1 {
                            fill: #000;
                            stroke: #666;
                            stroke-miterlimit: 10;
                        }</style>
                </defs>
                <g id="Слой_2" data-name="Слой 2">
                    <g id="Карточка">
                        <circle id="_Контур_" data-name="&lt;Контур&gt;" class="cls-1" cx="21" cy="21" r="20.5"/>
                        <path id="_Контур_2" data-name="&lt;Контур&gt;" class="cls-1"
                              d="M22.81,16.2V14.26a1,1,0,0,1,1.07-1.17H26.6V8.93l-3.75,0c-4.16,0-5.11,3.1-5.11,5.09v2.2H15.33v4.86h2.43V33.2h4.86V21.06h3.6l.17-1.91.27-2.95Z"/>
                    </g>
                </g>
            </svg>
        </a>
         <!-- i  -->
<!--        <a href="https://www.instagram.com/natali_bolgar/">-->
<!--            <svg width="43" height="43" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29 29">-->
<!--                <defs>-->
<!--                    <style>.soc-1 {-->
<!--                            fill:none;-->
<!--                            stroke:#4d4d4d;-->
<!--                            stroke-miterlimit:10;-->
<!--                        }</style>-->
<!--                </defs>-->
<!--                <g id="Слой_2" data-name="Слой 2">-->
<!--                    <g id="Карточка">-->
<!--                        <circle id="_Контур_" data-name="<Контур>" class="soc-1" cx="14.5" cy="14.5" r="14" style="stroke-width: 0.5px;stop-color: #4d4d4d;stroke: #000000;">-->
<!--                        </circle>-->
<!--                        <path class="soc-1" d="M9.48,8A2.91,2.91,0,0,0,7.86,9.57c-.44,1.12-.34,3.78-.34,5s-.1,3.91.34,5a2.91,2.91,0,0,0,1.62,1.62c1.12.44,3.78.34,5,.34s3.91.1,5-.34a2.91,2.91,0,0,0,1.62-1.62c.44-1.12.34-3.78.34-5s.1-3.91-.34-5A2.91,2.91,0,0,0,19.53,8c-1.12-.44-3.78-.34-5-.34S10.6,7.51,9.48,8Zm5,9.65a3,3,0,1,1,3-3A3,3,0,0,1,14.5,17.6Zm4.27-6.31a1,1,0,1,1,1-1A1,1,0,0,1,18.77,11.29Z" style="stroke-width: 0.5px;stop-color: #4d4d4d;stroke: #000000;">-->
<!--                        </path>-->
<!--                    </g>-->
<!--                </g>-->
<!--            </svg>-->
<!--        </a>-->
        <!-- !i  -->
        <!-- g+  -->
        <a href="https://plus.google.com/share?url=<?=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>" id="gplus">
            <svg width="43" height="43" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29 29">
                <defs>
                    <style>.soc-1{
                            fill:none;
                        }
                        .soc-1,
                        .soc-2{
                            stroke:#4d4d4d;
                            stroke-miterlimit:10;
                        }
                        .soc-2{
                            fill:#999;
                        }</style>
                </defs>
                <g id="Слой_2" data-name="Слой 2">
                    <g id="Карточка">
                        <circle id="_Контур_" data-name="<Контур>" class="soc-1" cx="14.5" cy="14.5" r="14" style="stroke-width: 0.5px;stop-color: #4d4d4d;stroke: #000000;">
                        </circle>
                        <path id="_Контур_2" data-name="<Контур>" class="soc-2" d="M21.45,10.61h-.3v-.27c0-.38,0-.77,0-1.15,0-.06-.07-.18-.11-.18-.35,0-.7,0-1.08,0v1.61H18.35c0,.12,0,.2,0,.28,0,.29,0,.58,0,.87l.85,0H20v1.59h1.16V11.79h1.59V10.61Z" style="stroke-width: 0.5px;stop-color: #4d4d4d;stroke: #000000;">
                        </path>
                        <path id="_Контур_3" data-name="<Контур>" class="soc-1" d="M18,13.36c-1.33,0-2.67,0-4,0-.38,0-.76,0-1.14,0-.07,0-.2.09-.2.14,0,.77,0,1.54,0,2.35h3.27c0,.16-.07.27-.11.38a3.16,3.16,0,0,1-3.68,2,3.6,3.6,0,0,1-3-4.28,3.47,3.47,0,0,1,.26-.82,3.6,3.6,0,0,1,4-2,3.44,3.44,0,0,1,1.53.8l1.79-1.8L16.56,10a5.88,5.88,0,0,0-5-1.36,6.11,6.11,0,0,0-5,5,6,6,0,0,0,.32,3.24,6.16,6.16,0,0,0,6.44,3.89A5.5,5.5,0,0,0,16.89,19a6.28,6.28,0,0,0,1.54-5.3C18.4,13.42,18.27,13.36,18,13.36Z" style="stroke-width: 0.5px;stop-color: #4d4d4d;stroke: #000000;">
                        </path>
                    </g>
                </g>
            </svg>
        </a>
        <!-- !g+  -->
<!--        <a href="http://pinterest.com/pin/create/button/?url=--><?//=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?><!--">-->
        <a class="pin" data-pin-do="buttonBookmark" data-pin-custom="true" data-pin-tall="true" data-pin-round="true" href="https://www.pinterest.com/pin/create/button/"><img src="<?=SITE_TEMPLATE_PATH?>/images/if_pinterest_12.png" height="47" style="position: relative;
    top: -4px;
    left: -4px;"/></a>
<!--            <svg width="43" height="43" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42 42">-->
<!--                <defs>-->
<!--                    <style>.cls-1 {-->
<!--                            fill: none;-->
<!--                            stroke: #666;-->
<!--                            stroke-miterlimit: 10;-->
<!--                        }</style>-->
<!--                </defs>-->
<!--                <g id="Слой_2" data-name="Слой 2">-->
<!--                    <g id="Карточка">-->
<!--                        <circle id="_Контур_" data-name="&lt;Контур&gt;" class="cls-1" cx="21" cy="21" r="20.5"/>-->
<!--                        <path id="_Контур_2" data-name="&lt;Контур&gt;" class="cls-1"-->
<!--                              d="M21.75,10.69c-5.82,0-8.75,4-8.75,7.4a6.71,6.71,0,0,0,.23,1.78,3.9,3.9,0,0,0,2.28,2.75.42.42,0,0,0,.61-.3l.25-1a.56.56,0,0,0-.18-.66,3.11,3.11,0,0,1-.67-1.22,4,4,0,0,1-.14-1.1,5.71,5.71,0,0,1,6-5.68c3.29,0,5.09,1.94,5.09,4.54a10.41,10.41,0,0,1-.23,2.19c-.51,2.37-1.86,4.11-3.65,4.11a1.85,1.85,0,0,1-1.93-2.28c.18-.72.43-1.46.65-2.16a7.58,7.58,0,0,0,.44-2,1.61,1.61,0,0,0-1.65-1.78c-1.31,0-2.36,1.31-2.36,3.07a4.86,4.86,0,0,0,.08.89,4,4,0,0,0,.31,1l-1.57,6.46a12.87,12.87,0,0,0,0,4.5.17.17,0,0,0,.29.07A12.55,12.55,0,0,0,19,27.37c.15-.53.86-3.26.86-3.26a3.54,3.54,0,0,0,3,1.48c3.21,0,5.57-2.3,6.34-5.67a11,11,0,0,0,.27-2.45C29.5,14,26.42,10.69,21.75,10.69Z"/>-->
<!--                    </g>-->
<!--                </g>-->
<!--            </svg>-->
<!--        </a>-->
    </div>
</div>

<?
global $res;
$res = $product->getRecomendedProduct();
$APPLICATION->IncludeComponent(
    "aniart:products.list",
    "recomended",
    [
        'CACHE_TYPE' => 'N',
        'CACHE_TIME' => 0,
        'FILTER' => ["ID"=>$res],
        'PAGE_VAR' => 'page',
        'PAGE_SIZE' => 12,
        'PROPERTY_CODE' => array('SIZE','COLOR')
    ]
);

$APPLICATION->IncludeComponent(
    "aniart:mneniya.comment",
    ".default",
    array(
        "CODE" => $product->getCode(),
        "COMPONENT_TEMPLATE" => ".default"
    ),
    false
);
?>

<?if(!empty($tags)):?>
    <section class="detail-tags">
        <div class="detail-tags-title">Идеальное платье где-то тут, очень близко!</div>
        <ul>
            <?foreach ($tags as $tag):?>
                <li><a href="<?=$tag['URL']?>">#<?=$tag['NAME']?></a></li>
            <?endforeach;?>
        </ul>
    </section>
<?endif;?>

<?//
//if(isset($_COOKIE["PRODUCT_VIEW"])) {
//if (substr_count($_COOKIE['PRODUCT_VIEW'], ",") == 0) {
//    $viewed = $_COOKIE["PRODUCT_VIEW"];
//} else {
//    $ar = explode(",", $_COOKIE["PRODUCT_VIEW"]);
//    array_unique($ar);
//    if(count($ar) > 20){
//        array_shift($ar);
//    }
//    $viewed = $ar;
//}
////    $APPLICATION->IncludeComponent(
////        "aniart:products.list",
////        "viewed1",
////        [
////            'CACHE_TYPE' => 'N',
////            'CACHE_TIME' => 0,
////            'FILTER' => ["ID" => $viewed],
////            'PAGE_VAR' => 'page',
////            'PAGE_SIZE' => 12,
////            'PROPERTY_CODE' => array('SIZE', 'COLOR')
////        ]
////    );
//}
//?>
<?
$productJson = Aniart\Main\Tools\Gtm::getDetailProductGtm($arResult['PRODUCT']);
$prevPage = Aniart\Main\Tools\Gtm::getPrevPage();
?>
<script type="text/javascript">
(function(d, s, id, host, ver, shopID, locale) {
    var js, fjs=d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    d.astrafitSettings={host:host,ver:ver,shopID:shopID,locale:locale};
    js=d.createElement(s); js.id=id; js.async=true;
    js.src=host+"/js/loader."+ver+".min.js";
    fjs.parentNode.insertBefore(js,fjs);
} ( document, "script", "astrafit-loader", "https://widget.astrafit.com", "latest", 263, "auto" ));



$(document).ready(function(){
    ProductDetailMain.initSlider({
        block: $(ProductDetailMain.block.carousel),
        vert: $(ProductDetailMain.selector.slider.vert),
        big: $(ProductDetailMain.selector.slider.big),
        img: $(ProductDetailMain.selector.slider.img)
    });
});
$(window).on('load', function(){
    App.Gtm.getDetailProduct(<?=$productJson?>, '<?=$prevPage?>');
    ProductDetailMain.lazyLoad();
});
/*google remarketing*/
var google_tag_params = {
    dynx_itemid: '<?=$product->getId();?>',
    dynx_pagetype: 'offerdetail',
    dynx_totalvalue: <?=$product->getPrice();?>
};

</script>
<script defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDw9ZZi-jAKKC9bwiz0QWi1p5AL5JoYVm8&callback=StoresProductListMain.initMap">
</script>
