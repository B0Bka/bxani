<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//new dBug($arResult, '', true);
?>

        <div class="show-mobile-menu">
        <?$APPLICATION->IncludeComponent(
            "bitrix:menu",
            "service",
            array(
                "ROOT_MENU_TYPE" => "m_service",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_TIME" => "360000",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => array(""),
                "MAX_LEVEL" => "1",
                "CHILD_MENU_TYPE" => "",
                "USE_EXT" => "N",
                "DELAY" => "N",
                "ALLOW_MULTI_SELECT" => "N"
            )
        );?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:menu",
            "company",
            array(
                "ROOT_MENU_TYPE" => "m_company",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_TIME" => "360000",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => array(""),
                "MAX_LEVEL" => "1",
                "CHILD_MENU_TYPE" => "",
                "USE_EXT" => "N",
                "DELAY" => "N",
                "ALLOW_MULTI_SELECT" => "N"
            )
        );?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:menu",
            "personal",
            array(
                "ROOT_MENU_TYPE" => "m_personal",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_TIME" => "360000",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => array(""),
                "MAX_LEVEL" => "1",
                "CHILD_MENU_TYPE" => "",
                "USE_EXT" => "N",
                "DELAY" => "N",
                "ALLOW_MULTI_SELECT" => "N"
            )
        );?>
        </div>
