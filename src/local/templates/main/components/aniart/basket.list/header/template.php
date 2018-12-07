<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$items = $arResult->getItems();
$basketGtm = Aniart\Main\Tools\Gtm::getOrderBasketGtm($arResult);
?>


<div class="close-basket">
    <svg width="29" height="29" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29 29">
        <defs>
            <style>.cls-11{fill:none;stroke:#4d4d4d;stroke-miterlimit:10;}</style>
        </defs>
        <g id="Слой_2" data-name="Слой 2">
            <g id="m_Поиск_01" data-name="m_Поиск 01">
                <circle class="cls-11" cx="14.5" cy="14.5" r="14"/>
                <line class="cls-11" x1="9.9" y1="9.9" x2="19.1" y2="19.1"/>
                <line class="cls-11" x1="9.9" y1="19.1" x2="19.1" y2="9.9"/>
            </g>
        </g>
    </svg>
</div>

<?if(!empty($items)):?>

<div class="bask-form">
    <div class="search-res">
        <div class="bask-title"><?=i18n('IN_BASKET')?></div>
        <div class="search-res-in">
        <?foreach($items as $item):?>
            <?
            $product = $item->getProduct();
            ?>
            <!-- Один товар -->
            <div class="one-search-res">
                <div class="one-s-thumb">
                <?foreach($product->getAllImagesId(1) as $key=>$img):?>
                    <?$img = $product->getMinPicture($img);?>
                    <img 
                        src="<?=$img['src']?>" 
                        alt="<?=$product->getName()?>" 
                        title="<?=$product->getName()?>"
                    />
                <?endforeach;?>
                </div>
                <div class="one-s-info">
                    <div class="one-s-tit">
                        <?=$product->getName()?>
                    </div>
                    <div class="one-s-det">
                        <?=i18n('COLOR')?>
                        <i 
                            style="border:1px solid #cccccc; background-image:url(<?=$product->getClothImg()?>);" 
                            class="fa fa-stop" 
                            aria-hidden="true"
                        ></i>,
                        <?=i18n('SIZE')?> <?=$item->getOffer()->getSize()?>
                        <?/* Не выводить количестов товаров
                            <?=$item->getQuantity()?> шт*/?>
                    </div>
                    <?if($item->getBasePrice() > $item->getMinPrice()){?>
                    <div class="one-s-price">
                        <span class="old-price">
                            <?=$item->getBasePrice(true)?>
                        </span>
                        <span class="new-price">
	                        <?=$item->getMinPrice(true)?>
                        </span>
                    </div>
                    <?} else {?>
                        <div class="one-s-price">
	                        <?=$item->getBasePrice(true)?>
                        </div>
                    <?}?>
                </div>
                <div 
                    class="bask-delete" 
                    style="font-weight:bold;cursor:pointer;" 
                    title="<?=i18n('DELETE')?>"
                >
                    <a data-id="<?=$item->getId()?>" data-product_id="<?=$item->getProductId()?>" href="javascript:void(0);">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <!-- Конец Один товар -->
        <?endforeach;?>
        </div>
        <div class="bask-sum">
            <div class="bask-price">
                <div class="bask-text-left"><?=i18n('TOTAL')?>:</div> <div class="bask-text-right"><?=$arResult->getDiscountPrice(true)?></div>
            </div>
            <div class="bask-order">
                <a href="<?=$arParams['PATH_TO_BASKET']?>" class="bask-path">
                    <?=i18n('TO_BASKET')?>
                </a>
                <a onclick="checkoutGtm()" href="<?=$arParams['PATH_TO_ORDER']?>" class="bask-order">
                    <?=i18n('MAKE_PURCHASE')?>
                </a>
            </div>
        </div>
    </div>
</div>
<script>
    function checkoutGtm()
    {
        var res;
        window.dataLayer = window.dataLayer || [];
        res = {
         'ecommerce': {
           'currencyCode': 'Currency Code',
           'checkout': {
             'actionField': {'step': 1},
             'products': <?=$basketGtm?>
           }
         },
         'event': 'gtmUaEvent',
         'gtmUaEventCategory': 'Enhanced Ecommerce',
         'gtmUaEventAction': 'Checkout Step 1',
         'gtmUaEventNonInteraction': 'False'
        };
        console.log(res);
        dataLayer.push(res);
    }
</script>
<?endif;?>