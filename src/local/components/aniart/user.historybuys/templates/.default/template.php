<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var AniartProductsListComponent $component
 * @var \Aniart\Main\Models\Product[] $products
 */
$component = $this->getComponent();
$basket_items = $arResult['BASKET_PRODUCTS'];
$products = $arResult['PRODUCT'];
$offers = $arResult['OFFER'];
$rec_ids = $arResult['RECOMMENT_IDS'];
$recommended_goods = $arResult['RECOMMEND_PRODUCTS'];


?>

<div class="your-hist">
    <div class="your-hist-tit">
        <div class="your-hist-tit-item">
            <span><?=i18n('GOOD') ?></span>
        </div>
        <div class="your-hist-tit-car">
            <span><?=i18n('RECOMMEND_PRODUCT') ?></span>
        </div>
    </div>
    <? if (count($basket_items) > 0): ?>
        <? foreach ($basket_items as $key => $item):?>
            <div class="your-one-hist">
                <div class="your-one-hist-item">
                    <div class="one-news">
                        <div class="one-cat-thumb">
                            <a href="<?=$products[$key]->getDetailPageUrl() ?>">
                                <?php
                                $imgData = $products[$key]->getAllImagesId(2);
                                ?>
                                <img

                                        src="<?=$products[$key]->getFilePath($imgData[0])?>"
                                        alt="<?=$products[$key]->getName()?>"
                                        title="<?=$products[$key]->getName()?>"
                                />
                            </a>
                        </div>
                        <div class="cat-new-info">
                            <div class="one-cat-tit">
                                <a href="<?=$products[$key]->getDetailPageUrl()?>">
                                   <?=$products[$key]->getName() ?>
                                </a>
                            </div>
                            <div class="one-cat-data">
                                <?=$item->getDate()?>
                            </div>
                            <div class="one-cat-color">
                                <div class="one-color">
                                    <?=i18n('SIZE')?>: <?=$arResult['OFFER'][$key]->getSize()?>
                                </div>

                            </div>
                            <div class="one-cat-color">
                                <div class="one-color">
                                  <span
                                          style="
                                                  position: relative;
                                                  display: block;
                                                  width: 30px;
                                                  height: 30px;
                                                  margin: 6px 0px 0px 8px;
                                                  cursor: pointer;
                                                  border:1px solid #cccccc;
                                                  background-image:url(<?=$arResult['PRODUCT'][$key]->getClothImg()?>);
                                                  "
                                          data-toggle="tooltip"
                                          data-placement="bottom"
                                          title="<?=$arResult['PRODUCT'][$key]->getCloth()?>"
                                  ></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?if(count($arResult['RECOMMENT_IDS'][$key]) > 0):?>
                <div class="we-rec">

                    <!-- Заголовок для моб -->
                    <div class="rec-mob-tit">
                        <?=i18n('RECOMMEND_PRODUCT') ?>
                    </div>
                    <!-- Конец Заголовок для моб -->

                    <div class="we-rec-in">
                    <?foreach ($arResult['RECOMMENT_IDS'][$key] as $rec_product):?>

                        <?
                        $APPLICATION->IncludeComponent('aniart:blank','catalog.history.item',
                            [
                            'IBLOCK_ID' => $arResult['RECOMMEND_PRODUCTS'][$rec_product]->getId(),
                            'CACHE_TYPE' => 'A',
                            'CACHE_TIME' => 36000,
                            'PRODUCT' => $arResult['RECOMMEND_PRODUCTS'][$rec_product],
                        ], $component)
                        ?>
                    <?endforeach?>
                    </div>
                </div>
                <?endif;?>
            </div>
        <? endforeach ?>
    <? endif; ?>
</div>
