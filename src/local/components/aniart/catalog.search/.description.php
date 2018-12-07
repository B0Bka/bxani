<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Поиск товаров',
	"DESCRIPTION" => 'Выборка ID товаров по поисковому запросу',
	"ICON" => "/images/news_list.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "AniArt",
		"CHILD" => array(
			"ID" => "aniart",
			"NAME" => 'Аниарт',
			"SORT" => 30,
		),
	),
);
?>