<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$catalogFilter['=ID'] = $arResult['ITEMS'];
$APPLICATION->SetTitle($arResult['PROMOTION']->getName());
?>
<div class="section-tit">
    <div class="section-tit">
        <?=i18n('ACTION_ITEMS')?>
    </div>
</div>
<div class="catalog">
    <div class="overflow"><img src="<?=SITE_TEMPLATE_PATH?>/images/loader.svg" style="width: 96px; height: 96px;"></div>
    <div id="catalog_products_list" class="catalog-in grid effect-2">
        <?
        $APPLICATION->IncludeComponent(
            'aniart:products.list',
            'main',
            [
                'CACHE_TYPE' => 'N',
                'CACHE_TIME' => 0,
                'FILTER' => $catalogFilter,
                'SORT' => ['SORT' => 'ASC'],
                'PAGE_VAR' => 'page',
                'PAGE_SIZE' => 10,
                'ADD_SECTIONS_CHAIN' => 'N',
                'ADD_ELEMENT_CHAIN' => 'N',
                'PROPERTY_CODE' => array('SIZE','COLOR')
            ],
            $component,
            ['HIDE_ICONS' => 'Y']
        );?>
    </div>
</div>

<div id="catalog_pagination" class="more-items">
    <a
            href="javascript:void(0);"
            data-sort="<?="SORT=ASC"?>"
            class="border"
    >
        <?=i18n('SHOW_MORE')?> <span></span>
    </a>
</div>