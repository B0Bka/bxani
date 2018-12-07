<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var AniartProductsListComponent $component
 * @var \Aniart\Main\Models\Product[] $products
 */
$component = $this->getComponent();
$products  = $arResult['ITEMS'];
?>
<div class="blog-page">
    <?if(!empty($arResult['SECTIONS'])):?>
        <div class="blog-cat">
            <ul>
                <li class="<?=$arResult['SECTION_ACTIVE_ALL']?>">
                    <a href="<?=$arParams['IBLOCK_URL']?>">
                        <?=i18n('ALL')?>
                    </a>
                </li>
                <?foreach($arResult['SECTIONS'] as $item):?>
                    <li class="<?=$item['ACTIVE']?>">
                        <a href="<?=$item['SECTION_PAGE_URL']?>">
                            <?=$item['NAME']?>
                        </a>
                    </li>
                <?endforeach;?>
            </ul>
        </div>
    <?endif;?>
    <div class="blog-page-in">
        <?if(!empty($arResult['ITEMS'])):?>
            <?foreach($arResult['ITEMS'] as $item):?>
                <!-- Один пост -->
                <div class="one-post-blog">
                    <div class="one-post-blog-in">
                        <div class = "img-container">
                            <img
                                src="<?=$item->getPicture()?>"
                                alt="<?=$item->getName()?>"
                                title="<?=$item->getName()?>"
                            />
                        </div>
                        <div class="one-post-descr">
                            <div class="one-post-descr-in">
                                <div class="one-post-blog-cat"></div>
                                <div class="one-post-blog-tit">
                                    <a href="<?=$item->getDetailPageUrl()?>">
                                        <?=$item->getName()?>
                                    </a>
                                </div>
                                <div class="one-post-blog-data">
                                    <?=$item->getDate()?>
                                </div>
                                <div class="one-post-blog-text">
                                    <?=$item->getPreviewText()?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Конец Один пост -->
            <?endforeach;?>
        <?endif;?>
    </div>
</div>
<br style="clear: both;"></br>
<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
    <?=$arResult['NAV_STRING']?>

<?endif;?>
<div id="catalog_pagination_num" class="in_blog"><div class="container"></div></div>
