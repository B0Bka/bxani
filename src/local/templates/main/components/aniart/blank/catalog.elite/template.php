<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @var  \Aniart\Main\Models\Product $product
 */
$product = $arParams['PRODUCT'];
$sibling = $product->getSiblingsFormated();
$imgData = $product->getMorePhotoElite(2);
$favs = \Aniart\Main\Repositories\FavoritesRepository::getFav();
in_array($product->getId(), $favs) ? $favorite = 'in-favorite' : $favorite = '';
$available = !empty($product->getAvailable())? 'Y' : 'N';
?>

<div class="one-cat-item cat-50 gtm-parent" id="<?=$product->getID()?>" <?=Aniart\Main\Tools\Gtm::getDataStr($product)?>>
    <?if($product->hasDiscount()):?>
        <div class="action-mark"><?=$product->showDiscountPercent()?></div>
    <?endif;?>
    <div class="one-cat-thumb" data-av="<?=$available?>">
	    <?view('catalog/product.favorite', ['product'=>$product, 'favorite'=>$favorite])?>
        <div class="product_img loader">
            <a href="<?=$product->getDetailPageUrl()?>" class="gtm-link">
                <?foreach($imgData as $key => $img):?>
                    <img 
                        src="<?=$img['src']?>" 
                        class="<?=($key > 0 ? 'hover-thumb' : '')?>" 
                        width="<?=$img['width']?>" 
                        height="<?=$img['height']?>" 
                        alt="<?=$product->getName()?>  1 - каталог Natali Bolgar"
                        title="<?=$product->getName()?> - фото 1"
	                    <?if($key == 0):?>
                            onload="CatalogProductsListMain.imageLoader(this)"
	                    <?endif;?>
                    />
                <?endforeach;?>
            </a>
        </div>
        <div class="mobile-open-bottom">
            <i>+</i>
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
        <?foreach($sibling['ITEMS'] as $key => $cloth):
            $firstPic = $key == 0 ? $imgData[0]['src'] : $cloth['elite'][0];
            $secondPic = $key == 0 ? $imgData[1]['src'] : $cloth['elite'][1];
            ?>
            <div class="product_list_size one-color">
                <label
                        class="<?=($cloth['id'] == $product->getId()?'checked':'')?>"
                        data-id="<?=$cloth['id']?>"
                        data-pic="<?=$firstPic?>"
                        data-thumb="<?=$secondPic?>"
                        data-url="<?=$cloth['url']?>"
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
            <div class="more-colors elite">
                <a href="<?=$product->getDetailPageUrl()?>">
                + <?=i18n('MORE').' '.declOfNum($sibling['DIFF'], [i18n('DECL_COLOR1'), i18n('DECL_COLOR2'), i18n('DECL_COLOR3')])?>
                </a>
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
            <span class="current-price"><?=i18n('SOLD_OUT')?></span>
		<?endif;?>
    </div>
</div>