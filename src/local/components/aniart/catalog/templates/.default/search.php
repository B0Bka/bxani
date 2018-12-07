<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var AniartCatalogComponent $component
 * @var \Aniart\Main\Models\ProductSection $section
 */
$component = $this->getComponent();
$section = $arResult['SECTION'];
$bannerTop = $section->getBannerTop();
$bannerBot = $section->getBannerBot();
$APPLICATION->SetTitle(i18n('SEARCH_TITLE'));
//$bannerBot4 = $section->getBannerBot4();

$arResult['SEF_CONTROLLER_CLASS']::bindFilteredPropsToUrl(
    $arResult['SEF_PAGE_URL'],
    $section->getId(),
    i18n()->lang()
);
if (registry()->isExists('sef_redirect')) {//component.php
    LocalRedirect(registry('sef_redirect'));
}

//new \dBug($arParams, '', true);
//dBug($section->getParentId());

?>
<?
global $USER;
$arGroups = CUser::GetUserGroup($USER->GetID());
?>
<?$searchFilter = $APPLICATION->IncludeComponent(
    'aniart:catalog.search',
    '',
    [
        'CACHE_TYPE' => 'N',
        'CACHE_TIME' => 36000,
        'FILTER' => $arSearchFilter,
    ],
    $component,
    ['HIDE_ICONS' => 'Y']
);
if($searchFilter):
    ?>
    <?/*6 - группа контент менеджер*/
    if (in_array(6, $arGroups) || in_array(1, $arGroups)):?>
        <button id="ChangePos">изменить расположение</button>
    <?endif;?>
    <!-- Сайдбар -->
    <aside class="sidebar">

        <div class="side-menu">
            <? if ($_SERVER['DOCUMENT_URI'] == '/catalog/index.php'): ?>
                <span class="menu-name">
                <?= $section->getName() ?>
            </span>
            <? else: ?>
                <h1 class="menu-name">
                    <?= $section->getName() ?>
                </h1>
            <? endif; ?>
            <? include "sidebar.php"; ?>
        </div>
        <!---Filter  -->
        <?
        /**
         * @var \Aniart\Main\Tools\FilterProperty $listProperties
         */

        $listProperties = app('FilterProperty');
        // new \dBug($listProperties);
        $catalogFilter = array_merge([
            'ACTIVE' => 'Y',
            'SECTION_ACTIVE' => 'Y',
            'IBLOCK_ID' => $section->getIblockId(),
            "GLOBAL_ACTIVE" => "Y",
        ],
            isset($catalogFilter) ? $catalogFilter : [],
            $searchFilter
        );

        $filter = $APPLICATION->IncludeComponent('aniart:custom.filter.oop', 'catalog',
            array_merge_recursive(
                [
                    'IBLOCK_ID' => $section->getIblockId(),
                    'FILTER_NAME' => 'filter',
                    'REQUEST_PAGE_URL' => $section->getUrl(),
                    //must be section url
                    'OFFERS_EXIST' => 'Y',
                    'OFFERS_IBLOCK_ID' => OFFERS_IBLOCK_ID,
                    'VIRTUAL_PROPERTIES' => [],
                    'MAIN_IBLOCK' => $section->getIblockId(),
                    'CACHE_TYPE' => 'N',
                    'CACHE_TIME' => 0,
                    'SECTION_ID' => $section->getId(),
                    'INCLUDE_SUBSECTIONS' => 'Y',
                    // подсчитывать кол-во товара во всех разделах начиная от текущего
                    'MORE_PROPERTY' => $catalogFilter,
                    // параметр, позволяющий ввести дополнительные условия в запрос для фильтра
                    'LANG' => i18n()->lang(),
                    'SEF_CONTROLLER' => $arResult['SEF_CONTROLLER_CLASS'],
                    'POPUP_POSITION' => 'left'
                ],
                [], //virtual
                $listProperties->GetPropertyList($section->getId(), [
                    CATALOG_PROP_COLOR => ['main_colors', 'select_colors'],
                ])
            ),
            false
        );
        $customFilter = $filter->ObtainFullBitrixFilter();
        if (is_array($customFilter)) {
            $catalogFilter = array_merge($catalogFilter, $customFilter);
        }
        ?>

    </aside>
    <!-- Конец Сайдбар -->

    <!-- Контент -->
    <div class="cont">

        <!-- Баннеры -->
        <?if(!empty($bannerTop) && !empty($bannerBot)):?>
            <div class="banners">

                <?if(!empty($bannerTop)):?>
                    <div class="top-banner">
                        <img src="<?=$bannerTop['img']['src']?>" alt=" " />
                        <?=$bannerTop['html']?>
                    </div>
                <?endif;?>
                <?if(!empty($bannerBot)):?>
                    <div class="bot-banner">
                        <?foreach($bannerBot as $bot):?>
                            <a href="javascript:void(0);">
                                <img src="<?=$bot['img']['src']?>" alt=" " />
                                <?=$bot['html']?>
                            </a>
                        <?endforeach;?>
                    </div>
                    <!--        <div class="bot-banner4">-->
                    <!--            --><?//foreach($bannerBot4 as $bot4):?>
                    <!--                <a href="javascript:void(0);">-->
                    <!--                    <img src="--><?//=$bot4['img']['src']?><!--" alt=" " />-->
                    <!--                    --><?//=$bot4['html']?>
                    <!--                </a>-->
                    <!--            --><?//endforeach;?>
                    <!--        </div>-->
                <?endif;?>
            </div>
        <?endif;?>
        <?/*$APPLICATION->IncludeComponent(
            'bitrix:main.include', '',
            array(
                'AREA_FILE_SHOW' => 'file',
                'PATH' => SITE_TEMPLATE_PATH . '/include/catalog_section_banner.php'
            ),
            false
        );*/?>

        <!-- Конец Баннеры -->

        <!-- Фильтр товаров -->
        <div class="cat-filter">
            <div class="cat-filter-in">

                <!-- Фильтр для планшета-моб -->
                <div class="filter-mob-bt">
                    <?= i18n('FILTER') ?>
                </div>
                <!-- Конец Фильтр для планшета-моб -->

                <?if($filter->PropertiesCount() > 0):?>
                    <?foreach ($filter->GetProperties() as $obProperty):?>
                        <?=$obProperty->GetHtml('select');?>
                    <?endforeach;?>
                <?endif;?>

                <?
                $sortVar = 'sort';
                $sortSelect = $_GET[$sortVar];
                $sortParams = [
                    'default' => [
                        'order' => ['sort' => 'ASC'],
                        'name' => i18n('DEFAULT')
                    ],
                    //                'default' => [
                    //                    'order' => $this->arParams['SORT'],
                    //                    'name' => i18n('DEFAULT')
                    //                ],
                    'bestseller' => [
                        'order' => $this->arParams['SORT'],
                        'name' => i18n('BESTSELLERS')
                    ],
                    'new' => [
                        'order' => $this->arParams['SORT'],
                        'name' => i18n('NEW')
                    ],
                    'expensive' => [
                        'order' => ['PROPERTY_MIN_PRICE' => 'DESC'],
                        'name' => i18n('PRICE_EXPENSIVE')
                    ],
                    'cheap' => [
                        'order' => ['PROPERTY_MIN_PRICE' => 'ASC'],
                        'name' => i18n('PRICE_CHEAP')
                    ]
                ];
                ?>
                <div id = "catalog_pagination_num">
                    <div class="container"></div>
                </div>

                <div class="cat-filter-right">
                    <div class="cat-select">
                        <select
                            id="catalog_sort"
                            <?/*onchange="window.location.href=this.options[this.selectedIndex].value" */?>
                            data-placeholder="<?=($sortSelect?$sortParams[$sortSelect]['name']:$sortParams['default']['name'])?>"
                        >
                            <option></option>
                            <?foreach($sortParams as $i=>$sort):?>
                                <option
                                    value="<?=$arResult['FOLDER'].'?'.$sortVar.'='.$i.'&q='.$_REQUEST['q']?>"
                                    data-sort="<?=$sortVar.'='.$i?>"
                                ><?=$sort['name']?></option>
                            <?endforeach;?>
                        </select>
                    </div>
                </div>

            </div>
        </div>
        <!-- Конец Фильтр товаров -->

        <div class="catalog">
            <div class="overflow"><img src="<?=SITE_TEMPLATE_PATH?>/images/loader.svg" style="width: 96px; height: 96px;"></div>
            <div id="catalog_products_list" class="catalog-in grid effect-2">

                <!-- Каталог -->
                <?
                $APPLICATION->IncludeComponent(
                    'aniart:products.list',
                    'main',
                    [
                        'CACHE_TYPE' => 'N',
                        'CACHE_TIME' => 0,
                        'FILTER' => $catalogFilter,
                        'SORT' => ['DATE_CREATE' => 'ASC'],
                        'SORT_DATA' => $sortParams,
                        'PAGE_VAR' => 'page',
                        'PAGE_SIZE' => 10,
                        'SORT_VAR' => $sortVar,
                        'ADD_SECTIONS_CHAIN' => 'Y',
                        'ADD_ELEMENT_CHAIN' => 'Y',
                        'PROPERTY_CODE' => array('SIZE','COLOR')
                    ],
                    $component,
                    ['HIDE_ICONS' => 'Y']
                );?>
                <!-- Конец Каталог -->

            </div>
        </div>
        <div id="catalog_pagination" class="more-items">
            <a
                href="javascript:void(0);"
                data-sort="<?="{$sortVar}={$sortSelect}"?>"
                class="border"
            >
                <?=i18n('SHOW_MORE')?> <span></span>
            </a>
        </div>

    </div>
<?else:?>
    <?= i18n('EMPTY_SEARCH_RESULT');?>
<?endif;?>
<button onclick="topFunction()" id="myBtn" title="Go to top"><span>&lsaquo;</span></button>
<!-- Конец Контент -->