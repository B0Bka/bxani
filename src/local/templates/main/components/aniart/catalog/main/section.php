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
use Aniart\Main\Repositories\TrendSectionRepository;

if($arResult['FOLDER'] == '/catalog/') $type = '';
    else $type = str_replace('/','',$arResult['FOLDER']);

$citiesRepository = new  \Aniart\Main\Repositories\MetatagsRepository(HL_META_ID);
$curMeta = reset($citiesRepository->getByUrl($APPLICATION->GetCurPage()));
if(!empty($curMeta)) $sectionText = $curMeta->getSeotext();

$component = $this->getComponent();
$section = $arResult['SECTION'];

if(empty($sectionText) && $type != 'sale')
    $sectionText = $section->getFields()["DESCRIPTION"];

$arResult['SEF_CONTROLLER_CLASS']::bindFilteredPropsToUrl(
    $arResult['SEF_PAGE_URL'],
    $section->getId(),
    i18n()->lang()
);
if (registry()->isExists('sef_redirect')) {//component.php
    LocalRedirect(registry('sef_redirect'));
}

//не выводить в корне каталога и распродажи фильтр и список товаров. только баннеры
if(\COption::GetOptionString("aniart.main", "show_filter_".$arResult['TYPE']) == 'N' && $arResult['ROOT_DIR'])
    $hideFilter = true;

global $USER;
$arGroups = CUser::GetUserGroup($USER->GetID());
?>
<?/*6 - группа контент менеджер*/
if ((in_array(6, $arGroups) || in_array(1, $arGroups)) && empty($searchFilter)):?>
    <a class="sort-link" style="display:none;" href="/sort/?id=<?=$section->getId()?>&page=<?=$_REQUEST['page']?>&type=<?=str_replace('/','',$arResult['FOLDER'])?>">изменить расположение</a>
<?endif;?>
<div class="catalog-wrap-full">
    <!-- Сайдбар -->
    <aside class="sidebar <?=$hideMenu ? 'hidden' : ''?>"">

        <div class="side-menu">
            <? include "sidebar.php"; ?>
        </div>
        <div class="side-filter <?=$hideFilter ? 'hidden' : ''?>">
        <!---Filter  -->
        <?

        /**
         * @var \Aniart\Main\Tools\FilterProperty $listProperties
         */

        $listProperties = app('FilterProperty');
        $catalogFilter = array_merge([
            'ACTIVE' => 'Y',
            'SECTION_ACTIVE' => 'Y',
            'IBLOCK_ID' => $section->getIblockId(),
            'SECTION_ID' => $section->getId(),
            "GLOBAL_ACTIVE" => "Y",
        ],
            isset($catalogFilter) ? $catalogFilter : [],
            isset($searchFilter) ? $searchFilter : [],
            isset($uniqFilter) ? $uniqFilter : []
        );
        if(empty($sectionUrl)) $sectionUrl = $section->getUrl();
        $filter = $APPLICATION->IncludeComponent('aniart:custom.filter.oop', 'catalog',
            array_merge_recursive(
                [
                    'IBLOCK_ID' => $section->getIblockId(),
                    'FILTER_NAME' => 'filter',
                    'REQUEST_PAGE_URL' => i18n()->getLangDir($this->__page == 'search' ? '/catalog/search/' : $sectionUrl),
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
        <!--    Filter end-->
        </div>
        <br style="clear: both">
    </aside>
    <!-- Конец Сайдбар -->

    <!-- Контент -->
    <div class="cont <?=!$hideMenu ? '' : 'full-width-cont'?>">
        <?
        /*
         * $gtmListType определяет list для просмотра списка или нажатия на товар
         */
        if($this->__page == 'sections' && $arResult['FOLDER'] == '/catalog/')
        {
            $bannerSection = BANNER_CATALOG_ID;
            $gtmListType = 'Category';
        }
        elseif($this->__page == 'uniq' && $arResult['FOLDER'] == '/sale/')
        {
            $bannerSection = BANNER_CATALOG_SALE_ID;
            $gtmListType = 'Sale';
        }
        elseif($this->__page == 'uniq' && $arResult['FOLDER'] == '/nashy_trendy/' && $sectionTrendId <= 0){
	        $bannerSection = BANNER_CATALOG_TREND_ID;
        }

        elseif($this->__page == 'search')
        {
            $gtmListType = 'Search';
        }
        elseif($this->__page == 'section')
        {
            $gtmListType = 'Category';
        }
        else $bannerSection = '';
        ?>
        <div class="banners-wrap <?=($arResult['FOLDER'] == '/nashy_trendy/') ? 'trand-banners' : ''?>">
        <?
        $APPLICATION->IncludeComponent(
            "aniart:banners",
            "catalog",
            [
                "SECTION_ID" => $bannerSection,
                "CATALOG_SECTION_ID" => $section->getId(),
                "TREND_SECTION_ID" => $sectionTrendId, //uniq.php
                "TYPE" => $type
            ],
            false
        );?>
        </div>
        <?if(!$hideFilter):?>
            <?
            $sortVar = 'sort';
            $sortSelect = $_GET[$sortVar];
            $sortParams = [
                'bestseller' => [
                    'order' => ['SORT' => 'ASC'],
                    'name' => i18n('BESTSELLERS')
                ],
                'new' => [
                    'order' => ['ID' => 'DESC'],
                    'name' => i18n('NEW')
                ],
                'expensive' => [
                    'order' => ['PROPERTY_MIN_PRICE' => 'ASC'],
                    'name' => i18n('PRICE_EXPENSIVE')
                ],
                'cheap' => [
                    'order' => ['PROPERTY_MIN_PRICE' => 'DESC'],
                    'name' => i18n('PRICE_CHEAP')
                ]
            ];
            ?>

            <!-- Фильтр товаров -->

            <div class="cat-filter <?=$hideSort || $hideMenu ? 'hidden' : ''?>">
                <div class="cat-filter-in <?=(app()->getDeviceType() == 'mobile') ? "cat-filter-in-mobile" : ""?>">

                    <?if(app()->getDeviceType() == 'mobile'){?>
    <!--                представление мобильных фильтров-->

                    <ul class="mob-filter-btn catalog-open mob-filter">
                        <li class="mobile-filter-caption">Каталог</li>
                        <li class="mobile-filter-content">
                            <i class="open-overflow"></i>
                            <ul>
                                <li class="mobile-catalog-menu">
                                    <?$APPLICATION->IncludeComponent(
                                        "bitrix:menu",
                                        "catalog_vertical",
                                        Array(
                                            "ALLOW_MULTI_SELECT" => "N",
                                            "CHILD_MENU_TYPE" => "catalog",
                                            "DELAY" => "N",
                                            "MAX_LEVEL" => "2",
                                            "MENU_CACHE_GET_VARS" => array(""),
                                            "MENU_CACHE_TIME" => "3600",
                                            "MENU_CACHE_TYPE" => "N",
                                            "MENU_CACHE_USE_GROUPS" => "Y",
                                            "ROOT_MENU_TYPE" => "catalog",
                                            "USE_EXT" => "Y"
                                        )
                                    );?>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="mob-filter-btn filter-open mob-filter <?=$hideFilter ? 'hidden' : ''?>" id="custom_filter">
                        <li class="mobile-filter-caption">фильтр</li>
                        <li class="mobile-filter-content">
                            <i class="open-overflow"></i>
                            <ul id="custom_filter_properties">
                                <li class="main-mobile-props">
                                    <ul>
                                        <li class="btn btn-black" id="reset-mobile-filter"><a href="/catalog/">Сбросить фильтр</a></li>
                                        <?if($filter->PropertiesCount() > 0):?>
                                        <?foreach ($filter->GetProperties() as $obProperty):
                                            if($obProperty->GetData()["CODE"] == "COLOR" || $obProperty->GetData()["CODE"] == "SIZES") {;?>
                                                <?/*NB-358 Фильтр над каталогом. Их всего 2. Цвет и размер (как самые употребляемые фильтры) */?>
                                                <?=$obProperty->GetHtml('main');?>
                                            <?}?>
                                            <?if($obProperty->GetData()["CODE"] == "MIN_PRICE"){?>
                                            <?=$obProperty->GetHtml('price');?>
                                        <?}?>
                                        <?endforeach;?>
                                        <li class="mob-filter-sort">
                                            <div class="">
                                                <span class="toggle-open-js"></span>
                                                    <select
                                                            id="catalog_sort" class="mobile-catalog-sort"
                                                        <?/*onchange="window.location.href=this.options[this.selectedIndex].value" */?>
                                                            data-placeholder="Сортировка:"
                                                    >
                                                        <option></option>
                                                        <?
                                                        if(!empty($section)) $url = $section->getUrl();
                                                        else $url = $arResult['FOLDER'];
                                                        if($type == 'sale') $url = str_replace('catalog', 'sale');
                                                        foreach($sortParams as $i=>$sort):?>
                                                            <option
                                                                    value="<?=$url.'?'.$sortVar.'='.$i?>"
                                                                    data-sort="<?=$sortVar.'='.$i?>"
                                                            ><?=$sort['name']?></option>
                                                        <?endforeach;?>
                                                    </select>

                                            </div>
                                        </li>
                                    </ul>
                                </li>

                                <li class = "more-properties">
                                    <ul>
                                        <?foreach ($filter->GetProperties() as $obProperty):
                                            if($obProperty->GetData()["CODE"] == "COLOR" ||
                                                $obProperty->GetData()["CODE"] == "SIZES" ||
                                                $obProperty->GetData()["CODE"] == "MIN_PRICE") {
                                                continue;?>
                                        <?}?>
                                            <?=$obProperty->GetHtml('main');?>
                                        <?endforeach;?>
                                    </ul>
                                </li>
                                <li class="more-properties-toggle-mobile btn btn-black">Дополнительные фильтры</li>
                                <li id="apply-filter-mobile" class="btn btn-white">Применить фильтр</li>
                            <?endif;?>
                            </ul>
                        </li>
                    </ul>
                        <script>
                            $('document').ready(function () {
                                try{
                                    App.CatalogFilter.getMobile();
                                    App.CatalogFilter.setDefaultFilterPrices();
                                    App.CatalogFilter.addMobileFilterBtnProperties();
                                }
                                catch (err){
                                    console.log(err);
                                }
                            })
                        </script>
    <!--                представление мобильных фильтров конец-->
                    <?}?>

                    <!-- Фильтр для планшета-моб -->
    <!--                <div class="filter-mob-bt dropdown-toggle" data-toggle="dropdown" data-target=".filt-mobile">-->
    <!--		            --><?//= i18n('FILTER') ?>
    <!--                </div>-->
    <!--                <div class="filter-mob-btn">-->
    <!--                    <input id="custom_filter_active_mob" type="button" value="Применить" onclick="App.CatalogFilter.submitFilter()" style="visibility: visible;">-->
    <!--                </div>-->
                    <!-- Конец Фильтр для планшета-моб -->

                    <div class="filt-mobile <?=$hideFilter ? 'hidden' : ''?>">
                        <?if(app()->getDeviceType() != "mobile"){?>
                            <?if($filter->PropertiesCount() > 0):?>
                                <?foreach ($filter->GetProperties() as $obProperty):
                                    if($obProperty->GetData()["CODE"] == "COLOR" || $obProperty->GetData()["CODE"] == "SIZES") {;?>
                                        <?/*NB-358 Фильтр над каталогом. Их всего 2. Цвет и размер (как самые употребляемые фильтры) */?>
                                        <?=$obProperty->GetHtml('select');?>
                                    <?}?>
                                <?endforeach;?>
                            <?endif;?>
                        <?}?>
                        <div class="reset-filt mobile">
                            <input
                                    id="custom_filter_reset_mob"
                                    type="button"
                                    value="<?=i18n('RESET_FILTER')?>"
                                    onclick="App.CatalogFilter.resetFilter('<?=isset($_GET['sort']) ? 'sort=' . $_GET['sort'] : ''?>')"
                            >
                        </div>
                    </div>


                    <div id = "catalog_pagination_num" <?=$hideFilter ? 'class="hidden"' : ''?>>
                        <div class="container"></div>
                    </div>

                    <?if(app()->getDeviceType() != 'mobile' && !$hideFilter){?>
                    <div class="filter-mob-bt dropdown-toggle" data-toggle="dropdown" data-target=".cat-filter-right">
                        <?= i18n('SORT') ?>
                    </div>
                    <div class="cat-filter-right">
                        <div class="cat-select">
                            <select
                                    id="catalog_sort"
                                <?/*onchange="window.location.href=this.options[this.selectedIndex].value" */?>
                                    data-placeholder="<?=($sortSelect?$sortParams[$sortSelect]['name']:i18n('DEFAULT'))?>"
                            >
                                <option></option>
                                <?
                                if(!empty($section)) $url = $section->getUrl();
                                else $url = $arResult['FOLDER'];
                                if($type == 'sale') $url = str_replace('catalog', 'sale');
                                foreach($sortParams as $i=>$sort):?>
                                    <option
                                            value="<?=$url.'?'.$sortVar.'='.$i?>"
                                            data-sort="<?=$sortVar.'='.$i?>"
                                    ><?=$sort['name']?></option>
                                <?endforeach;?>
                            </select>
                        </div>
                    </div>
                    <?}?>

                </div>
            </div>
            <!-- Конец Фильтр товаров -->

            <div class="catalog">
                <div class="overflow"><img src="<?=SITE_TEMPLATE_PATH?>/images/loader.svg" style="width: 96px; height: 96px;"></div>
                <div id="catalog_products_list" data-gtm="<?=$gtmListType?>" class="catalog-in grid effect-2">
                    <?if(isset($catalogFilter['=ID']) && $catalogFilter['=ID'] != 'empty'){?>
                        <p class="search-results">
                            <span><h1 class="search">Результаты поиска по запросу: <span class="bold">"<?=$_GET['q']?>".</span></h1></span>
                        </p>
                    <?} else if(isset($catalogFilter['=ID'])){?>
                    <p class="search-results">
                        <span><h1 class="search">К сожалению, не удалось ничего найти по вашему запросу <span class="bold">"<?=$_GET['q']?>".</span> <br>
                                Попробуйте поискать что-нибудь другое или посмотрите наши новинки:</h1></span>
                    </p>
                        <div id="novlety">
                        <?
                        $APPLICATION->IncludeComponent(
                            "aniart:products.list",
                            "novelty",
                            [
                                'CACHE_TYPE' => 'N',
                                'CACHE_TIME' => 0,
                                'FILTER' => ["!PROPERTY_NOVELTY" => false],
                                'PAGE_VAR' => 'page',
                                'PAGE_SIZE' => 12,
                                'PROPERTY_CODE' => array('SIZE','COLOR'),
                            ]
                        );
                        ?>
                        </div>
    <!--                    чтобы не включалась подгрузка при скролле -->
                        <script>
                            $('document').ready(function () {
                               $('body').find('#catalog_pagination').remove();
                               <?if($hideMenu):?>
                                    App.Catalog.setCatalogBlockCenter();
                               <?endif;?>
                            })
                        </script>
                    <?}?>
                    <!-- Каталог -->
                    <?$APPLICATION->IncludeComponent(
                        'aniart:products.list',
                        'main',
                        [
                            'CACHE_TYPE' => 'N',
                            'CACHE_TIME' => 0,
                            'FILTER' => $catalogFilter,
                            'SORT' => ['SORT' => 'ASC'],
                            'SORT_DATA' => $sortParams,
                            'PAGE_VAR' => 'page',
                            'PAGE_SIZE' => 10,
                            'SORT_VAR' => $sortVar,
                            'ADD_SECTIONS_CHAIN' => 'Y',
                            'ADD_ELEMENT_CHAIN' => 'Y',
                            'PROPERTY_CODE' => array('SIZE','COLOR'),
                            'TYPE' => str_replace('/','',$arResult['FOLDER'])
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
        <?endif;?>
        <?if(!empty($sectionText) && empty($_REQUEST['page'])):?>
            <div class="section-description">
                <?=$sectionText?>
            </div>
        <?endif;?>
    </div>
    <br style="clear: both">
</div>
    <button onclick="topFunction()" id="myBtn" title="Go to top"><span>&lsaquo;</span></button>
    <!-- Конец Контент -->

<?
if(!$section->isNew() && empty($type))
{
    seo()->fetchSeoParams($section);
}
?>
<?if($hideMenu):?>
<script>
    $('document').ready(function () {
        App.Catalog.setCatalogBlockCenter();
    })
</script>
<?endif;?>
