<?
$component = $this->getComponent();
$products  = $arResult['PRODUCTS'];
?>
<section id="viewed-items" data-gtm="Recently">
    <div class="section-tit">
        <div class="section-tit">
            Просмотренныe товары
        </div>
    </div>
    <div class="car-news">
        <div class="container">
            <div class="car-watch-in owl-carousel">
                <?foreach ($arResult["ITEMS"] as $item):

                    //dBug($item);

                    $APPLICATION->IncludeComponent("aniart:blank", "catalog.viewed", array(
                        "PRODUCT" => $item,
                        "IMAGE_WIDTH" => 105,
                        "IMAGE_HEIGHT" => 105));
                    endforeach;?>
            </div>
        </div>
    </div>
</section>
