<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();

} ?>
<?
if (!empty($arResult["CATEGORIES"])):?>
    <div class="search-res">
        
        <div class="search-res-in">
            <?
            foreach ($arResult["CATEGORIES"] as $category_id => $arCategory):?>

                <?
                foreach ($arCategory["PRODUCTS"] as $i => $product):
                    $discountPrice = $product->getPrice();
                    $price = $product->getBasePrice();
                    ?>

                    <a href="<?echo $product->getDetailPageUrl() ?>">
                        <?php
                        $imgData = $product->getAllImagesId(2);
                        $image = $product->getFilePath($imgData[0]);
                        ?>
                    <div class="one-search-res">
                        <div class="one-s-thumb">
                            <img src="<?=$image ?>">
                        </div>
                        <div class="one-s-info">
                            <div class="one-s-tit"><?=$product->getName() ?></div>

                            <?if($discountPrice < $price):?>
                                <span class="one-s-old-price">
                                    <?=$product->getBasePrice(true)?>
                                </span>
                                <span class="one-s-discount-price">
                                    <?=$product->getPrice(true)?>
                                </span>
                            <?else:?>
                                <?=$product->getPrice(true)?>
                            <?endif;?>
                        </div>
                        </div>

                    </a>


                <? endforeach ?>
            <? endforeach ?>
        </div>

        <div class="all-rez">
            <a href="<?echo $arResult["CATEGORIES"]['all']['ITEMS'][0]["URL"] ?>">
            <?echo $arResult["CATEGORIES"]['all']['ITEMS'][0]["NAME"] ?>
            </a>
        </div>
    </div>

<?endif;
?>