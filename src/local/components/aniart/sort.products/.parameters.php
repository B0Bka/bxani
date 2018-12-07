<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"CACHE_TIME" => array("DEFAULT" => "3600"),
		"SECTION_ID" => Array(
			"PARENT" => "BASE",
			"NAME"=>GetMessage("SECTION_ID"),
			"TYPE"=>"STRING",
		)
	),
);
?> 