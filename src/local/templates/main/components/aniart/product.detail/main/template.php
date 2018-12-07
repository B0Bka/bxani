<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Aniart\Main\FavoritesTable,
    \Aniart\Main\Repositories\FavoritesRepository;

$product = $arResult['PRODUCT'];
$currentOffer = $arResult['CURRENT_OFFER'];
$sibling = $product->getSibling();
$APPLICATION->SetTitle($product->getName());
$favs = FavoritesRepository::getFav();
$available = $product->getAvailable();
$allImages = $product->getAllImagesId();
?>
<?ob_start();?>
<!-- ТОвар -->
<div id="fb-root"></div>
<script>
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.11';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<div id="product_detail" class="product">
    <embed hidden = "true" data-vieved = "<?=$product->getId()?>" id="viewed"></embed>
    <div hidden="true" itemscope itemtype="http://schema.org/Product">
        <span itemprop="brand">Natali Bolgar</span>
        <span itemprop="name"><?= $product->getName() ?></span>
        <img itemprop="image" src="<?=$product->getFilePath($product->getAllImagesId()[0])?>"/>
        <span itemprop="description"><?=$arResult['PRODUCT']->getDetailText();?></span>
        <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <meta itemprop="priceCurrency" content="<?=$product->getCurrency()?>"/>
            <span itemprop="price"><?=$product->getBasePrice()?></span>
            <span itemprop="availability" href="http://schema.org/InStock"/><?= $product->isActive() == true ? "В наличии" : "Нет в наличии" ?></span>
        </span>
    </div>
    <!-- Заголовок и цвета для моб -->
    <div class="prod-mob">
        <h1>
            <?=$product->getName()?>
        </h1>
        <div class="free-shiping">
            <?//=i18n('FREE_SHIPPING')?>
        </div>
        <div class="art">
            <?=i18n('VENDOR_CODE')?>: <?=$product->getArticle()?>
        </div>
        <div class="prod-color">
            <div class="prod-color-name">
                <?=i18n('COLOR')?>: <span><?=$product->getClothDescription()?></span>
            </div>

            <?foreach($sibling as $cloth):?>
                <div class="one-color">
                    <label class="<?=($cloth['id'] == $product->getId()?'checked':'')?>">
                        <span style="border: 1px solid #cccccc; background-image: url(<?=$cloth['img']?>);"></span>
                        <input
                                type="radio"
                                name="<?=$cloth['id']?>"
                                value="<?=$cloth['id']?>"
                                data-url="<?=$cloth['url']?>"
                        />
                    </label>
                </div>
            <?endforeach;?>

        </div>
    </div>
    <!-- Конец Заголовок и цвета для моб -->

    <!-- Карусель -->
    <?if(!empty($product->getAllImagesId())):?>
        <div id="product_detail_car" class="prod-car <?=(count($product->getAllImagesId()) <= 3?'no-car':'')?>">
            <div class="load"><div class="load-in"></div></div>
            <!-- Вертикальная карусель -->
            <div class="prod-car-vert">
                <div id="product_detail_car_vert" class="prod-car-vert-in" style="visibility: hidden">
                    <? foreach ($product->getAllImagesId() as $i => $img): ?>
                    <?$pic = $product->getMinPicture($img, 100, 150);?>
                        <a data-slide-index="<?= $i ?>" href="">
                            <img
                                    src="<?= $pic['src'] ?>"
                                <? if ($i == 0): ?>
                                    alt="<?= $product->getName() ?> - интернет-магазин Natali Bolgar"
                                    title="<?= $product->getName() ?> – Natali Bolgar"
                                <? else: ?>
                                    alt="<?= $product->getName() . ' ' . $i ?> - интернет-магазин Natali Bolgar"
                                    title="<?= $product->getName() . ' ' . $i ?> – Natali Bolgar"
                                <? endif; ?>
                            />
                        </a>
                    <? endforeach; ?>
                </div>
            </div>
            <!-- Конец Вертикальная карусель -->

            <!-- Основная карусель товара -->
            <div class="prod-car-big">
                <!-- Иконка увеличения -->
                <div class="zoom"></div>
                <!-- Конец Иконка увеличения -->
                
                <?if($product->hasDiscount()):?>
                    <div class="action-mark"><?=$product->showDiscountPercent()?></div>
                <?endif;?>
                    
                <div id="product_detail_car_big" class="prod-car-big-in">
                    <?foreach($product->getOptimizedImagesId() as $i => $img):?>
                        <img
                            xoriginal="<?= $product->getFilePath($allImages[$i]) ?>"
                            <? if ($i == 0): ?>
                                src="<?= $product->getFilePath($img) ?>"
                                alt="<?= $product->getName() ?> - интернет-магазин Natali Bolgar"
                                title="<?= $product->getName() ?> – Natali Bolgar"
                            <? else: ?>
                                src="/local/templates/main/images/loader.svg"
                                data-src="<?= $product->getFilePath($img) ?>"
                                class="lazy-slider-img"
                                alt="<?= $product->getName() . ' ' . $i ?> - интернет-магазин Natali Bolgar"
                                title="<?= $product->getName() . ' ' . $i ?> – Natali Bolgar"
                            <? endif; ?>
                        />
                    <?endforeach;?>
                </div>
            </div>
            <!-- Конец Основная карусель товара -->
        </div>
    <?endif;?>
    <!-- Конец Карусель -->

    <!-- Основная информация о товара -->
    <div class="product-descr">
        <form>
            <?if($product->getFreeShipping()):?>
                <div class="prod-title">
                    <h1>
                        <?=$product->getName()?>
                    </h1>
                    <div class="free-shipping">
                        <?//=i18n('FREE_SHIPPING')?>
                    </div>
                </div>
            <?else:?>
                <h1>
                    <?=$product->getName()?>
                </h1>
            <?endif;?>

            <div class="art">
                <?=i18n('VENDOR_CODE')?>: <?=$product->getArticle()?>
            </div>
            <div class="prod-price">
                <?if(!empty($available)):?>
                    <?if($product->hasDiscount()):?>
                        <span class = "was-price">
                            <?=$product->getMaxPrice(true)?>
                        </span> &nbsp;
                        <span class="new-price"><?=$product->getPrice(true)?></span>
                    <?else:?>
                        <?=$product->getPrice(true)?>
                    <?endif;?>
                <?else:?>
                    <span class="not-available-price"><?=i18n('SOLD_OUT')?></span>
                <?endif;?>
            </div>
            <div class="infos">

                <div class="prod-color">
                    <div class="prod-color-name">
                        <?=i18n('COLOR')?>: <span><?=$product->getClothDescription()?></span>
                    </div>

                    <?foreach($sibling as $cloth):?>
                        <div class="one-color">
                            <label class="<?=($cloth['id'] == $product->getId()?'checked':'')?>">
                                <span style="border: 1px solid #cccccc; background-image: url(<?=$cloth['img']?>);"></span>
                                <input
                                        type="radio"
                                        name="<?=$cloth['id']?>"
                                        value="<?=$cloth['id']?>"
                                        data-url="<?=$cloth['url']?>"
                                />
                            </label>
                        </div>
                    <?endforeach;?>

                </div>

                <div class="prod-size">
                    <div class="prod-size-left">
                        <div class="prod-size-name">
                            <?=i18n('SIZE')?>:
                            <span id="product_detail_size"><?=!empty($available) ? i18n('NOT_SELECTED') : i18n('NOT_AVAILABLE_SIZES')?></span>
                        </div>
                        #SIZE_BLOCK#
                    </div>

                    <?//= p($arResult)?>

                    <div class="size-pr">
                        <!-- AstraFit.button -->
                        <div class="astrafit-wdgt"
                             data-id="<?=$product->getAstrafitCode()?>"
                             data-align="left"
                             data-size="md">
                            <div class="prod-size-right astrafit">
                                <div class="prod-size-name">
                                    <?=i18n('DRESSING_ROOM')?>:
                                </div>
                            </div>
                            <!-- /AstraFit.button -->
                        </div>
                    </div>
                </div>
                <div class="prod-table">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#size-table">
                        <?=i18n('SIZE_TABLE')?>
                    </a>
                </div>
                <!-- Цена для моб -->
                <div class="prod-mob">
                    <div class="prod-price new-price">
                <span>
                    <?//discount price?>
                </span>
                        <?if($product->getMaxPrice() != $product->getBasePrice()){?>
                            <span class="old-price"><?=$product->getMaxPrice(true); $action = 'action'?></span>
                        <?} else {$action = 'n-action';}?>
                        <span class="<?=$action?>"><?=$product->getBasePrice($product->getCurrency())?></span>


                    </div>
                </div>
                <!-- Конец Цена для моб -->
                <div class="prod-bt">
                    <div id="product_detail_buy" class="prod-bay">
                        <a href="javascript:void(0);" class="disabled">
                            <?=i18n('BUY')?>
                        </a>
                    </div>
                    <div class="size-alert">Пожалуйста, выберите размер</div>
                    <div class="prod-more-fav">
                        <div class="prod-more">
                            <a
                                    id="product_detail_availability"
                                    href="javascript:void(0);"
                                    class="product-detail-availability disabled"
                            ><?=i18n('AVAILABILITY_IN_STORES')?></a>
                        </div>
                        <div class="prod-fav">
                            <a class="fav <?if(in_array($product->getId(), $favs)) echo 'in-favorite'?>" href="#" data-item="<?=$product->getId()?>"></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
<!-- Конец ТОвар -->
<?
$this->__component->arResult["CACHED_TPL"] = @ob_get_contents();
ob_get_clean();
?>
