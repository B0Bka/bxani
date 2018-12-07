<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Aniart\Main\FavoritesTable,
    \Aniart\Main\Repositories\FavoritesRepository;

$product = $arResult['PRODUCT'];
$currentOffer = $arResult['CURRENT_OFFER'];
$sibling = $product->getSiblingSorted();
$basketService = app('BasketService');
$basketProductsId = $basketService->getProductIds();
$favs = FavoritesRepository::getFav();
$allImages = $product->getAllImagesId();
?>
<!-- ТОвар -->

<div class="product-popup">
    <div class="popup-head">
        <span>Быстрая покупка</span>
    </div>

    <!-- Заголовок и цвета для моб -->
    <div class="prod-mob">
        <h1>
            <?=$product->getName()?>
        </h1>

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
        <div id="product_detail_car-popup" class="prod-car <?=(count($product->getAllImagesId()) <= 3?'no-car':'')?>">
            <?if($arParams['SHOW_SMALL_PICS'] == 'Y'):?>
                <!-- Вертикальная карусель -->
                <div class="prod-car-vert">
                    <div id="product_detail_car_vert-popup" class="prod-car-vert-in">
                        <? foreach ($product->getAllImagesId() as $i => $img):
                            $pic = $product->getMinPicture($img, 100, 150);?>
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
            <?endif;?>
            <!-- Основная карусель товара -->
            <div class="prod-car-big">
                <!-- Иконка увеличения -->
                <div class="zoom"></div>
                <!-- Конец Иконка увеличения -->

                <?if($product->hasDiscount()):?>
                    <div class="action-mark">%</div>
                <?endif;?>

                <div id="product_detail_car_big-popup" class="prod-car-big-in">
                    <?foreach($product->getOptimizedImagesId() as $i => $img):?>
                        <img
                            src="<?= $product->getFilePath($img) ?>"
                            xoriginal="<?= $product->getFilePath($allImages[$i]) ?>"
                            alt="<?= $product->getName() ?> - интернет-магазин Natali Bolgar"
                            title="<?= $product->getName() ?> – Natali Bolgar"
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
            <h1>
                <?=$product->getName()?>
            </h1>
            <div class="swap-mobile">
            <div class="art">
                <?=i18n('VENDOR_CODE')?>: <?=$product->getArticle()?>
            </div>
            <div class="prod-price">
                <?if($product->hasDiscount()):?>
                <span class = "was-price">
                    <?=$product->getMaxPrice(true)?>
                </span>
                    <span class="new-price"><?=$product->getPrice(true)?></span>
                    <?else:?>
                    <?=$product->getPrice(true)?>
                <?endif;?>

            </div>
            </div>
            <div class="infos">

                <div class="prod-color">
                    <div class="prod-color-name">
                        <?=i18n('COLOR')?>: <span><?=$product->getClothDescription()?></span>
                    </div>

                    <?foreach($sibling as $cloth):?>
                        <div class="one-color">
                            <label class="<?=($cloth['id'] == $product->getId()?'checked':'')?>">
                                <a class="set-reload" data-item="<?=$cloth['id']?>" href="javascript:void(0);">
                                    <span style="border: 1px solid #cccccc; background-image: url(<?=$cloth['img']?>);"></span>
                                </a>
                                <?/*
                                <input
                                        type="radio"
                                        name="<?=$cloth['id']?>"
                                        value="<?=$cloth['id']?>"
                                        data-url="<?=$cloth['url']?>"
                                />
                                */?>
                            </label>
                        </div>
                    <?endforeach;?>

                </div>

                <div class="prod-size">
                    <div class="prod-size-left">
                        <div class="prod-size-name">
                            <?=i18n('SIZE')?>:
                            <span id="product_detail_size"><?=i18n('NOT_SELECTED')?></span>
                        </div>
                        <?if($product->showSizeTypes()):
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
                                    <div class="size-info"><div class="size-info-pic"><span class=tooltiptext>'.i18n('SIZE_INFO').'</span></div></div>';
                            echo($result);
                        endif;?>
                        <div class="size-list">
                        <?
                            $sizes = $product->getOffersSize();
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
                                if(isset($size['name'])) {
                                    echo '<div class="one-color">
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
                            ?>
                        </div>
                    </div>
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
                        <div class="prod-fav-popup">
                            <a class="fav <?=in_array($product->getId(), $favs) ? 'in-favorite' : 'not-favorite'?>" href="#" data-item="<?=$product->getId()?>">
                                <span id="popup-add-fav-label">Добавить в лист желаний</span>
                                <span id="popup-del-fav-label">Убрать из списка желаний</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="bottom-mobile-slider">
        <span>В этом образе</span>
    </div>
    <div class="popup-bottom-link">
        <a href="<?=$product->getUrl()?>">Перейти на страницу товара</a>
    </div>
    <br style="clear: both;">
</div>

<!-- Конец ТОвар -->