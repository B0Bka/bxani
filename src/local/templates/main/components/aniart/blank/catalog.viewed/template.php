<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @var  \Aniart\Main\Models\Product $product
 */
$product = $arParams['PRODUCT'];
$sibling = $product->getSiblingsFormated();
$imgData = $product->getMorePhotoPreview();
$favs = \Aniart\Main\Repositories\FavoritesRepository::getFav();
in_array($product->getId(), $favs) ? $favorite = 'in-favorite' : $favorite = '';
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
        <a href="<?=$product->getDetailPageUrl()?>" class="gtm-link">
            <?=$product->getName()?>
        </a>
    </div>
    <div class="one-cat-sizes-aval">
	    <?view('catalog/product.sizes.avaliable', ['product'=>$product])?>
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