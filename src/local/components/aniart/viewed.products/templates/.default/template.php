<section>
    <div class="section-tit">
        <h3>
            <?=i18n("VIEWED")?>
        </h3>
    </div>
    <div class="car-news">
        <div class="container">
            <div class="car-watch-in">
                <?foreach ($arResult["ITEMS"] as $item):
                    $APPLICATION->IncludeComponent("aniart:blank", "history.item", array(
                            "PRODUCT" => $item,
                        "IMAGE_WIDTH" => 105,
                        "IMAGE_HEIGHT" => 105));
                    endforeach;?>
            </div>
        </div>
    </div>
</section>
