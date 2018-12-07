<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var AniartProductsListComponent $component
 * @var \Aniart\Main\Models\Product[] $products
 */
$component = $this->getComponent();
$products = $arResult['PRODUCT'];
$offers = $arResult['OFFERS'];
$rec_ids = $arResult['RECOMMENT_IDS'];
$recommended_goods = $arResult['RECOMMEND_PRODUCTS'];
$retailItems = $arResult['RETAIL_PRODUCTS'];
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
    <?
    if (count($retailItems) > 0): ?>
        <? foreach ($retailItems as $key => $retailItem):
            $item = $products[$offers[$key]];
            if(empty($item) || empty($arResult['OFFER'][$key])) continue;
            ?>
            <div class="your-one-hist">
                <div class="your-one-hist-item">
                    <div class="one-news">
                        <div class="one-cat-thumb">
                            <a href="<?=$item->getDetailPageUrl() ?>">
                                <?php
                                $imgData = $item->getAllImagesId(2);
                                ?>
                                <img

                                        src="<?=$item->getFilePath($imgData[0])?>"
                                        alt="<?=$item->getName()?>"
                                        title="<?=$item->getName()?>"
                                />
                            </a>
                        </div>
                        <div class="cat-new-info">
                            <div class="one-cat-tit">
                                <a href="<?=$item->getDetailPageUrl()?>">
                                   <?=$item->getName() ?>
                                </a>
                            </div>
                            <div class="one-cat-data">
                                <?=$retailItem['DATE']?>
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
                                                  background-image:url(<?=$item->getClothImg()?>);
                                                  "
                                          data-toggle="tooltip"
                                          data-placement="bottom"
                                          title="<?=$item->getCloth()?>"
                                  ></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?
                if(!empty($arResult['RECOMMENT_IDS'][$item->getId()])):?>
                <div class="we-rec">

                    <!-- Заголовок для моб -->
                    <div class="rec-mob-tit">
                        <?=i18n('RECOMMEND_PRODUCT') ?>
                    </div>
                    <!-- Конец Заголовок для моб -->

                    <div class="we-rec-in">
                    <?foreach ($arResult['RECOMMENT_IDS'][$item->getId()] as $rec_product):?>

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
        <?endforeach ?>
    <? endif;?>
</div>
<?if($arResult['PAGINATION']['NavPageCount'] > 1):?>
    <div id="catalog_pagination" class="more-items">
        <a
                href="javascript:void(0);"
                class="border"
        >
            <?=i18n('SHOW_MORE')?> <span></span>
        </a>
    </div>
<?endif;?>
