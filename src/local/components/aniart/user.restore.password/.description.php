<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Восстановление пароля',
	"DESCRIPTION" => 'Авторизация после перехода с письма восстановления пароля',
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