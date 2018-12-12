<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle('Главная');
?>
<? $APPLICATION->IncludeComponent(
	"aniart:register", 
	"main", 
	array(
		"COMPONENT_TEMPLATE" => "main",
		"SHOW_FIELDS" => array(
			0 => "EMAIL",
			1 => "NAME",
			2 => "LAST_NAME",
			3 => "PERSONAL_BIRTHDAY",
			4 => "PERSONAL_PHONE",
			5 => "PERSONAL_MOBILE",
			6 => "PERSONAL_CITY",
			7 => "WORK_COMPANY",
			8 => "WORK_POSITION",
			9 => "PASSWORD",
			10 => "CONFIRM_PASSWORD"
		),
		"REQUIRED_FIELDS" => array(
			0 => "EMAIL",
			1 => "NAME",
			2 => "LAST_NAME",
			3 => "PERSONAL_PHONE",
			4 => "PERSONAL_MOBILE",
			5 => "PERSONAL_CITY",
			6 => "WORK_COMPANY",
			7 => "WORK_POSITION",
			8 => "PASSWORD",
			9 => "CONFIRM_PASSWORD"
		),
		"AUTH" => "Y",
		"USE_BACKURL" => "N",
		"SUCCESS_PAGE" => "",
		"SET_TITLE" => "N",
		"USER_PROPERTY" => array(
			0 => "UF_VOEN",
			1 => "UF_TYPE",
			2 => "UF_WHATSAPP",
		)
	),
	false
); ?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>