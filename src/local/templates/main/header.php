<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Page\Asset;

IncludeTemplateLangFile(__FILE__);
$baskerService = app('BasketService');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <?/*<link rel="shortcut icon" href="/favicon.png">*/?>
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <?
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/nouislider.min.css');
    $APPLICATION->ShowHead();
    $APPLICATION->ShowHeadStrings();
    ?>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <script src="http://css3-mediaqueries-js.googlecode.com/files/css3-mediaqueries.js"></script>
    <![endif]-->

    <?/*<link href="<?= SITE_TEMPLATE_PATH ?>/fix.css?v=29102018" rel="stylesheet" type="text/css"/>*/?>
</head>

<body>
<div id="bitrix_panel" class="bitrix-panel"><?= $APPLICATION->ShowPanel(); ?></div>

<div class="wrapper">

    <!-- Шапка -->
    <header>
        <div class="header-top">
            <div class="container">

                        <span>
                    <? $APPLICATION->IncludeComponent(
                        'bitrix:main.include', '',
                        array(
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include/phone_1.php'
                        ),
                        false
                    ); ?>
                    <? $APPLICATION->IncludeComponent(
                        'bitrix:main.include', '',
                        array(
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include/phone_2.php'
                        ),
                        false
                    ); ?>
                    <? $APPLICATION->IncludeComponent(
                        'bitrix:main.include', '',
                        array(
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => SITE_TEMPLATE_PATH . '/include/header_address.php'
                        ),
                        false
                    ); ?>
                    </span>
                </div>
            <? $APPLICATION->IncludeComponent(
                "aniart:search.title",
                "catalog",
                Array(
                    "CATEGORY_0" => array("iblock_1c_catalog"),
                    "CONTAINER_ID" => "title-search",
                    "INPUT_ID" => "title-search-input",
                    "NUM_CATEGORIES" => "1",
                    "ORDER" => "date",
                    "PAGE" => "#SITE_DIR#catalog/search/",
                    "SHOW_INPUT" => "Y",
                    "SHOW_OTHERS" => "N",
                    "TOP_COUNT" => "4",
                    "USE_LANGUAGE_GUESS" => "Y"
                )
            ); ?>
            <?/*
                <div class="header-bt">
                    <? if (!$USER->IsAuthorized()): ?>
                        <a class="bt-1 modal-open" data-toggle="modal" data-target="#myModal">
                        !
                        </a>
                    <? else: ?>
                        <a class="bt-1" title="<?= i18n('PERSONAL', 'PERSONAL') ?>"
                           href="/personal/">
                            @
                        </a>
                    <? endif; ?>


                    <? if ($baskerService->getItemsCount() > 0): ?>
                        <span><?= $baskerService->getItemsCount() ?></span>
                    <? endif; ?>
                        <!-- Блок Корзины -->
                    <div id="basket_list" class="basket-pop"></div>
                        <!-- Конец Блок корзины -->
                </span>

                </div>
*/?>
                <div class="logo">
                       <?/* <a href="/">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/logo.png" alt=""/>
                        </a>
*/?>
                </div>
            </div>
        </div>
        <? $APPLICATION->IncludeComponent(
            "bitrix:menu",
            "top",
            array(
                "COMPONENT_TEMPLATE" => "top",
                "ROOT_MENU_TYPE" => "top",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => array(),
                "MAX_LEVEL" => "1",
                "CHILD_MENU_TYPE" => "left",
                "USE_EXT" => "Y",
                "DELAY" => "N",
                "ALLOW_MULTI_SELECT" => "N"
            ),
            false
        ); ?>
    </header>
    <!-- Конец Шапка -->

    <!-- Основная часть -->
    <main class="main <?= $APPLICATION->AddBufferContent('getPageClass') ?>
                      <?=($mainDetect && ERROR_404 != 'Y')?'home-page':''?>">

        <? if ($mainDetect && ERROR_404 != 'Y'): ?>
            <?
//main slider
?>
            <div class="main-slider">
            <?
            $APPLICATION->IncludeComponent('aniart:slider', 'main', [
                'IBLOCK_TYPE' => 'content',
                'IBLOCK_ID' => MAIN_SLIDER_IBLOCK_ID,
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => 36000
            ], false);

            /*$APPLICATION -> IncludeComponent(
                'aniart:blank',
                'main_slider',
                [
                    'CACHE_TYPE'=>'Y',
                    'CACHE_TIME' => '3600',
                    'LANG' => strtoupper(i18n()->lang()),
                    'COUNT' => '10',
                    'IBLOCK_ID' => 'main_slider'
                ]
            );*/
            ?>
            </div>
        <? endif; ?>

        <div class="container">

            <?if (showBreadcrumb()): ?>
                <!-- Хлебные крошки -->
                <? $APPLICATION->IncludeComponent(
                    'bitrix:breadcrumb',
                    'main',
                    [
                        'START_FROM' => '0',
                        'PATH' => '',
                        'SITE_ID' => 's1'
                    ]
                ); ?>
                <!-- Конец Хлебные крошки -->

                <? if ($APPLICATION->GetDirProperty('static') === 'Y'): ?>
                    <div class="page-content">
                <? endif; ?>

                <?if(detectPersonal()):?>
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "personal",
                        array(
                            "COMPONENT_TEMPLATE" => "personal",
                            "ROOT_MENU_TYPE" => "left",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_CACHE_GET_VARS" => array(),
                            "MAX_LEVEL" => "1",
                            "CHILD_MENU_TYPE" => "left",
                            "USE_EXT" => "N",
                            "DELAY" => "N",
                            "ALLOW_MULTI_SELECT" => "N"
                        ),
                        false
                    ); ?>
                <?endif;?>
            <? endif;?>
