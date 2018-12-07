<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
        "CACHE_KEY" => Array(
            "PARENT" => "BASE",
            "NAME" => "Ключ для кеша",
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
		"CACHE_TIME" => array("DEFAULT" => "3600"),
	),
);
?> 