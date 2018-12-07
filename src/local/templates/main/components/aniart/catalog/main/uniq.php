<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Aniart\Main\Repositories\TrendSectionRepository;
/*
 * Для распродажи установить фильтр по чекбоксу Скидка
 * и подменить параметр для компонента фильтра
 */
$hideSort = false; //не выводить панель сортировки, если в тренде нет товаров или стоит галочке в свойстве раздела тренда
$hideMenu = false; //не выводить левое меню на десктопе, если в тренде стоит галочка
$arDir = explode('/', $APPLICATION->GetCurDir());
if($arResult['TYPE'] == 'sale')
{
    $uniqFilter['!PROPERTY_SALE'] = false;
    $sectionUrl = str_replace('catalog', 'sale', $arResult['SECTION']->getUrl());
    if(!empty($arDir[2]))
    {
        $meta = new Aniart\Main\Seo\SaleMetaGenerator($arResult['SECTION']);
        $arMeta = $meta->getMeta();
        seo()->fetchSeoParamsByArray($arMeta);
        $uniqH1 = $meta->getH1();
    }
    else $uniqH1 = 'Sale';
}
/*
 * Для трендов
 */
elseif($arResult['TYPE'] == 'nashy_trendy')
{
    $productsRepository = app('ProductsRepository');
    if(!empty($arDir[2]))
    {
        $trendRepository = new TrendSectionRepository(TREND_MENU_IB);
        $sectionTrend = $trendRepository->getByCode($arDir[2]);
        if ($sectionTrend) {
            $sectionTrend->setBreadcrumbs();
            $trendProducts = $productsRepository->getList([],['=PROPERTY_TREND' => $sectionTrend->getId(), 'ACTIVE' => 'Y']);

            if(empty($trendProducts) || $sectionTrend->hideSort())
                $hideSort = true;

            $detect = new \Mobile_Detect;
            if($sectionTrend->hideMenu() && !$detect->isMobile())
                $hideMenu = true;
            
            $sectionTrendId = $sectionTrend->getId();
            $sectionText = $sectionTrend->getDescription();
            $uniqFilter['PROPERTY_TREND'] = $sectionTrendId;
            $sectionUrl = '/nashy_trendy/'.$arDir[2].'/';
            $meta = new Aniart\Main\Seo\TrendsMetaGenerator($sectionTrend);
            $arMeta = $meta->getMeta();
            seo()->fetchSeoParamsByArray($arMeta);
        }
        else
        {
            \Bitrix\Iblock\Component\Tools::process404('', true, true, true);
        }

    }
    else
    {
        $uniqFilter['!PROPERTY_TREND'] = false;
        $sectionUrl = '/nashy_trendy/';
        $trendProducts = $productsRepository->getList([],['!PROPERTY_TREND' => false, 'ACTIVE' => 'Y']);
        if(empty($trendProducts) || \COption::GetOptionString("aniart.main", "show_trends_sort") != 'Y') $hideSort = true;
    }
}
include 'section.php';
?>