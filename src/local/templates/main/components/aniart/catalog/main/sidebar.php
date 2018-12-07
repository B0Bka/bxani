<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/2/2017
 * Time: 5:53 PM
 */

/*
 * В каталоге вывести меню раздела и тренды
 */
if($arResult['FOLDER'] == '/catalog/'):?>
<div class="menu-block">
    <? if ($_SERVER['DOCUMENT_URI'] == '/catalog/index.php'): ?>
        <span class="menu-name">
            <?= $section->getName() ?>
        </span>
    <? else: ?>
        <h1 class="menu-name">
            <?= $section->getName() ?>
        </h1>
    <? endif;
    $APPLICATION->IncludeComponent(
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
</div>
<div class="menu-block">
    <span class="menu-name">
        <?=i18n('TRANDS')?>
    </span>
    <?$APPLICATION->IncludeComponent(
        "bitrix:menu",
        "catalog_vertical",
        Array(
            "ALLOW_MULTI_SELECT" => "N",
            "CHILD_MENU_TYPE" => "trend",
            "DELAY" => "N",
            "MAX_LEVEL" => "2",
            "MENU_CACHE_GET_VARS" => array(""),
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_TYPE" => "N",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "ROOT_MENU_TYPE" => "trend",
            "USE_EXT" => "Y"
        )
    );?>
</div>
<?
    /*
 * В трендах вывести меню трендов потом меню каталога
 */
elseif($arResult['FOLDER'] == '/nashy_trendy/'):?>
<div class="menu-block">
    <h1 class="menu-name">
        <?=!(empty($sectionTrend)) ? $sectionTrend->getName(): i18n('TRANDS')?>
    </h1>
    <?$APPLICATION->IncludeComponent(
        "bitrix:menu",
        "catalog_vertical",
        Array(
            "ALLOW_MULTI_SELECT" => "N",
            "CHILD_MENU_TYPE" => "trend",
            "DELAY" => "N",
            "MAX_LEVEL" => "2",
            "MENU_CACHE_GET_VARS" => array(""),
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_TYPE" => "N",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "ROOT_MENU_TYPE" => "trend",
            "USE_EXT" => "Y"
        )
    );?>
</div>
<div class="menu-block">
    <span class="menu-name">
        <?= $section->getName() ?>
    </span>
    <?
    $APPLICATION->IncludeComponent(
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
</div>
<?/*
 * В остальных случаях меню каталога
 */
else:?>
<div class="menu-block">
    <h1 class="menu-name">
        <?= !empty($uniqH1) ? $uniqH1 : $section->getName() ?>
    </h1>
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
</div>
<?
endif;

