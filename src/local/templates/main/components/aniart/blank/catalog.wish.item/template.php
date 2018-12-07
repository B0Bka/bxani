<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @var  \Aniart\Main\Models\Product $product
 */
$product = $arParams['PRODUCT'];
$sibling = $product->getSibling();
$imgData = $product->getAllImagesId(2);

?>

<div class="one-news">
    <div class="one-cat-thumb">
        <div class="del-fav" data-item="<?=$product->getId()?>">
            <a href="javascript:void(0);"></a>
        </div>
        <a href="<?=$product->getDetailPageUrl()?>">
            <?if(!empty($imgData)):?>
                <?foreach($imgData as $key => $img):?>
                    <img 
                        class="<?=($key > 0 ? 'hover-thumb' : '')?>" 
                        src="<?=$product->getFilePath($img)?>" 
                        alt="<?=$product->getName()?>" 
                        title="<?=$product->getName()?>"
                        />
                <?endforeach;?>
            <?else:?>
                <img  
                    src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png" 
                    alt="<?= $product->getName() ?>" 
                    title="<?=$product->getName() ?>"
                />
            <?endif;?>
        </a>
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

<!--    <div class="one-cat-color">-->
<!--        <form>-->
        <?/*
        foreach($sibling as $color):?>
            <div class="product_list_size one-color">

                <label
                    class="<?=($color->getId() == $product->getId()?'checked':'')?>"
                    data-id="<?=$color->getId()?>"
                    data-target="#size"
                >
                    <span style="border: 1px solid #cccccc; background-image: url(<?=$color->getClothImg()?>);"></span>
                    <input
                        type="radio"
                        name="<?=$color->getId()?>"
                        value="<?=$color->getId()?>"
                    />
                </label>
            </div>
        <?endforeach;*/
?>
<!--        </form>-->
<!--    </div>-->
    <div class="one-cat-price new-price">
        <span>
            <?/*2110 грн*/?>
        </span>
        <?=$product->getPrice(true)?>
    </div>
</div>