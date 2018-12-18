<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Смена пароля',
	"DESCRIPTION" => '',
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