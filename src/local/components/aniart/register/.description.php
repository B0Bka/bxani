<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
		"NAME" => 'register',
		"DESCRIPTION" => 'registration',
		"ICON" => "/images/cat_list.gif",
		"CACHE_PATH" => "Y",
		"SORT" => 30,
		"PATH" => array(
				"ID" => "aniart",
				"CHILD" => array(
						"ID" => "reviews_aniart",
						"NAME" => GetMessage("T_REGISTER"),
						"SORT" => 30,
				),
		),
);

?>