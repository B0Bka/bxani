<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @var  \Aniart\Main\Models\Product $product
 */
$product = $arParams['PRODUCT'];
$sibling = $product->getSibling();
$favs = \Aniart\Main\Repositories\FavoritesRepository::getFav();
in_array($product->getId(), $favs) ? $favorite = 'in-favorite' : $favorite = '';
$imgData = $product->getMorePhotoPreview();
$available = !empty($product->getAvailable())? 'Y' : 'N';
?>
<?/**?>
<a href="<?=$product->getDetailPageUrl()?>"><div class="mobile-overflow"></div></a>
<?/**/?>
<div class="one-news">
<?if($product->hasDiscount()):?>
    <div class="action-mark">%</div>
<?endif;?>
    <div class="one-cat-thumb">
	    <?view('catalog/product.favorite', ['product'=>$product, 'favorite'=>$favorite])?>
        <div class="product_img">
            <a href="<?=$product->getDetailPageUrl()?>">
            <?foreach($imgData as $key => $img):?>
                <img 
                    src="<?=$img['src']?>" 
                    class="<?=($key > 0 ? 'hover-thumb' : '')?>" 
                    width="<?=$img['width']?>" 
                    height="<?=$img['height']?>" 
                    alt="<?=$product->getName()?>" 
                    title="<?=$product->getName()?>"
                />
            <?endforeach;?>
            </a>
        </div>
        <div class="fast-look one-cat-by" data-target="#size">
            Быстрый просмотр
        </div>
    </div>
    <div class="one-cat-tit">
        <a href="<?=$product->getDetailPageUrl()?>">
            <?=$product->getName()?>
        </a>
    </div>
    <div class="one-cat-sizes-aval">
	    <?view('catalog/product.sizes.avaliable', ['product'=>$product])?>
    </div>
    <div class="one-cat-color">
    <?foreach($sibling as $key => $cloth):?>
	    <?$sibling['DIFF'] = $key - 3;?>
        <div class="product_list_size one-color">
            <?if($key < 4){?>
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
            <?} else {?>
                <div class="more-colors">
                    <a href="<?=$product->getDetailPageUrl()?>">
                        + <?=i18n('MORE').' '.declOfNum($sibling['DIFF'], [i18n('DECL_COLOR1'), i18n('DECL_COLOR2'), i18n('DECL_COLOR3')])?>
                    </a>
                </div>
            <?}?>
        </div>
    <?endforeach;?>
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
            <span class="current-price"><?=i18n('SOLD_OUT')?></span>
        <?endif;?>
    </div>
</div>
&nbsp;