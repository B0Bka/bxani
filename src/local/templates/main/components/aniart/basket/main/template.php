<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//new \dBug($arParams, '', true);
//dBug($arResult);
//new \dBug($arResult->getItems(), '', true);
$items = $arResult->getItems();
$totalPrice = 0;
?>

<!-- Заголовок для моб -->
<div class="lk-tit">
    <?=i18n('BASKET')?>
</div>
<!-- Заголовок для моб -->

<?if(!empty($items)):?>

<div class="basket-page-in">

    <!-- Один блок -->
    <div class="basket-row basket-top">
        <div class="basket-name">
            <?=i18n('PRODUCT')?>
        </div>
        <div class="basket-row-in">
            <div class="basket-row-inside">
                <div class="basket-color">
                    <?=i18n('COLOR')?>
                </div>
                <?/**/?>
                <div class="basket-size">
                    <?=i18n('SIZE')?>
                </div>
                <?/**?>
                <div class="basket-num">
                    <?=i18n('QUANTITY')?>
                </div>
                <?/**/?>
                <div class="basket-price">
                    <?=i18n('PRICE')?>
                </div>
                <?/*
                <div class="basket-sum">
                    <?=i18n('ONLY')?>
                </div>
                */?>
            </div>
        </div>
    </div>
    <!-- Конец Один блок -->
    
    <?foreach($arResult->getItems() as $item):
        $product = $item->getProduct();
        $siblings = $item->getSiblings();
        $totalPrice += $item->getMinPrice() * $item->getQuantity();
    ?>
    <!-- Один блок -->
    <div class="basket-row">
        <div class="basket-name">
            <div class="basket-name-in">
                <div class="basket-thumb">
                    <a href="<?=$product->getDetailPageUrl()?>">
                    <?foreach($product->getAllImagesId(1) as $key=>$img):?>
                        <?$img = $product->getMinPicture($img);?>
                        <img 
                            src="<?=$img['src']?>" 
                            alt="<?=$product->getName()?>" 
                            title="<?=$product->getName()?>"
                        />
                    <?endforeach;?>
                    </a>
                </div>
                <div class="basket-item-descr">
                    <div class="basket-item-tit">
                        <a href="<?=$product->getDetailPageUrl()?>">
                            <?=$product->getName()?>
                        </a>
                        <div class="bask-art">
                            <?=i18n('VENDOR_CODE')?>: <?=$product->getArticle()?>
                        </div>
                    </div>
                </div>
                <div class="del-from">
                    <a href="javascript:void(0);" data-id="<?=$item->getId()?>">
                        <?=i18n('DELETE')?>
                    </a>
                </div>
            </div>
        </div>
        <div class="basket-row-in">
            <div class="basket-row-inside">
                <div class="basket-color">
                    <div class="name-cell-mob">
                        <?=i18n('COLOR')?>
                    </div>
                    <div class="bask-sel colors">
                        
                        <?/*<select data-placeholder=" ">
                        <?foreach($siblings as $i=>$sibling):?>
                            <option <?=($i == $product->getId()?'selected':'')?>><?=$sibling->getCloth()?></option>
                        <?endforeach;?>
                        </select>*/?>
                        
                        <div class="multi-sel">
                            <div class="multi-tit color-dropdown">
                                <span 
                                    style="
                                        background-image:url(<?=$product->getClothImg()?>);
                                    " 
                                    data-toggle="tooltip" 
                                    data-placement="bottom" 
                                    title="<?=$product->getClothDescription()?>"
                                ></span>
                            </div>
                            <ul class="filt-color">
                                <li>
                                    <label>
                                        <span 
                                            style="border:1px solid #cccccc;background-image:url(<?=$product->getClothImg()?>);" 
                                            data-toggle="tooltip" 
                                            data-placement="bottom" 
                                            title="<?=$product->getCloth()?>"
                                        ></span>
                                        <input 
                                            type="checkbox" 
                                        >
                                    </label>
                                </li>
                            <?/*foreach($siblings as $i=>$sibling):?>
                                <?if($i == $product->getId()) continue;?>
                                <li>
                                    <label>
                                        <span 
                                            style="border:1px solid #cccccc;background-image:url(<?=$sibling->getClothImg()?>);" 
                                            data-toggle="tooltip" 
                                            data-placement="bottom" 
                                            title="<?=$sibling->getCloth()?>"
                                        ></span>
                                        <input 
                                            type="checkbox" 
                                        >
                                    </label>
                                </li>
                            <?endforeach;*/?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="basket-size">
                    <div class="name-cell-mob">
                        <?=i18n('SIZE')?>
                    </div>
                    <div class="bask-sel">
                        <select data-placeholder=" ">
                            <option><?=$item->getOffer()->getSize()?></option>
                        </select>
                    </div>
                </div>
                <?/*Не выводить количестов товаров
                <div class="basket-num">
                    <div class="name-cell-mob">
                        <?=i18n('QUANTITY')?>
                    </div>
                    <div class="basket_quantity bask-sel">
                        <select data-id="<?=$item->getId()?>" data-placeholder=" ">
                        <?foreach($quantity as $quan):?>
                            <option value="<?=$quan['ID']?>" <?=($quan['SELECTED']?'selected':'')?>><?=$quan['ID']?></option>
                        <?endforeach;?>
                        </select>
                    </div>
                </div>
                */?>
                <div class="basket-price">
                    <?=$item->getMinPrice(true)?>
                </div>
                <?/*Не выводить количестов товаров
                <div class="basket-sum">
                    <span class="pr-mob"><?=i18n('ONLY')?>: </span>
                    <?=$item->getTotalBasePrice(true)?>
                </div>
                */?>
            </div>
        </div>
    </div>
    <!-- Конец Один блок -->
    <?endforeach;?>
</div>
<div class="basket-all-info">
    <?/*<div class="basket-all-left">
        <div class="one-inp-bask">
            <input class="one-inp-bask-in" type="text" placeholder="<?=i18n('ENTER_PROMOCODE')?>">
            <button class="one-inp-bask-bt"><?=i18n('USE')?></button>
        </div>
        <div class="one-inp-bask">
            <input class="one-inp-bask-in" type="text" placeholder="<?=i18n('ENTER_GIFT_CARD')?>">
            <button class="one-inp-bask-bt"><?=i18n('USE')?></button>
        </div>
    </div>*/?>
    <div class="basket-all-right">
        <div class="basket-all-sum">
            <span>
                <?=i18n('TOTAL')?>:
            </span>
            <b>
                <?//=$arResult->getPrice(true)?>
                <?=$arResult->formatPrice($totalPrice, true)?>
            </b>
        </div>
        <div class="basket-all-bt">
            <a href="<?=$arParams['PATH_TO_ORDER']?>">
                <input class="border-black" type="button" value="<?=i18n('MAKE_PURCHASE')?>">
            </a>
        </div>
    </div>
</div>

<?else:?>

<div class="basket-all-info">
    В корзине пусто
</div>

<?endif;?>
