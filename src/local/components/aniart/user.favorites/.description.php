<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
		"NAME" => 'user.favorites',
		"DESCRIPTION" => 'user.favorites',
		"ICON" => "/images/cat_list.gif",
		"CACHE_PATH" => "Y",
		"SORT" => 30,
		"PATH" => array(
				"ID" => "aniart",
				"CHILD" => array(
						"ID" => "reviews_aniart",
						"NAME" => GetMessage("T_IBLOCK_DESC_REVIEWS"),
						"SORT" => 30,
						"CHILD" => array(
								"ID" => "catalog_cmpx",
						),
				),
		),
);

?>