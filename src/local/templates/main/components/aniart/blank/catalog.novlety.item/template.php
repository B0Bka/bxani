<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @var  \Aniart\Main\Models\Product $product
 */
$product = $arParams['PRODUCT'];
$sibling = $product->getSiblingsFormated();
$imgData = $product->getMorePhotoPreview();
$favs = \Aniart\Main\Repositories\FavoritesRepository::getFav();
$available = !empty($product->getAvailable())? 'Y' : 'N';
?>
<?/**?>
<a href="<?=$product->getDetailPageUrl()?>"><div class="mobile-overflow"></div></a>
<?/**/?>
<div class="one-news gtm-parent" <?=Aniart\Main\Tools\Gtm::getDataStr($product)?>>
<?if($product->hasDiscount()):?>
    <div class="action-mark"><?=$product->showDiscountPercent()?></div>
<?endif;?>
    <div class="one-cat-thumb">
	    <?view('catalog/product.favorite', ['product'=>$product, 'favorite'=>$favorite])?>
        <div class="product_img">
            <a href="<?=$product->getDetailPageUrl()?>" class="gtm-link">
                <?foreach($imgData as $key => $img):?>
                    <?$alt = $title = $key+1?>
                    <img
                            src="<?=$img['src']?>"
                            class="<?=($key > 0 ? 'hover-thumb' : '')?>"
                            width="<?=$img['width']?>"
                            height="<?=$img['height']?>"
                            alt="<?=$product->getName() .' '. $alt?> - Новинка от Natali Bolgar"
                            title="<?=$product->getName() . ' ' . $title?> - новинка"
                    />
                <?endforeach;?>
            </a>
        </div>
        <div class="fast-look one-cat-by" data-target="#size">
            Быстрый просмотр
        </div>
    </div>
    <div class="one-cat-tit">
        <a href="<?=$product->getDetailPageUrl()?>" class="gtm-link">
            <?=$product->getName()?>
        </a>
    </div>
    <div class="one-cat-sizes-aval">
	    <?view('catalog/product.sizes.avaliable', ['product'=>$product])?>
    </div>
    <div class="one-cat-color">
        <?
        foreach($sibling['ITEMS'] as $cloth):?>
            <div class="product_list_size one-color">
                <label
                        class="<?=($cloth['id'] == $product->getId()?'checked':'')?>"
                        data-id="<?=$cloth['id']?>"
                >
                    <span style="border: 1px solid #cccccc; background-image: url(<?=$cloth['img']?>);"></span>
                    <input
                            type="radio"
                            name="<?=$cloth['id']?>"
                            value="<?=$cloth['id']?>"
                    />
                </label>
            </div>
        <?endforeach;?>
        <?if($sibling['DIFF'] > 0):?>
            <div class="more-colors">
                + <?=i18n('MORE').' '.declOfNum($sibling['DIFF'], [i18n('DECL_COLOR1'), i18n('DECL_COLOR2'), i18n('DECL_COLOR3')])?>
            </div>
        <?endif;?>
    </div>
    <div class="one-cat-price new-price">
        <?if($available == 'Y'):?>
            <?if($product->hasDiscount()):?>
                <span class="old-price">
                <?=$product->getMaxPrice(true)?>
            </span>
                <span class="current-price"><?=$product->getPrice(true)?></span>
            <?else:?>
                <?=$product->getPrice(true)?>
            <?endif;?>
        <?else:?>
            <span class="current-price">Снят с продажи</span>
        <?endif;?>
    </div>
</div>
&nbsp;