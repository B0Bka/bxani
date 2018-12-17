<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle('Главная');
?>

<? $APPLICATION->IncludeComponent(
	"aniart:auth",
	"main",
	array(
	),
	false
); ?>
<span class="tab client"><?=i18n('CLIENT_TAB', 'auth')?></span>
<span class="tab partner"><?=i18n('PARTNER_TAB', 'auth')?></span>

<? $APPLICATION->IncludeComponent(
	"aniart:register",
	"main",
	array(
		"COMPONENT_TEMPLATE" => "main",
		"SHOW_FIELDS" => array(
			0 => "EMAIL",
			1 => "NAME",
			2 => "LAST_NAME",
			3 => "PERSONAL_PHONE",
			4 => "PERSONAL_CITY",
			5 => "PASSWORD",
			6 => "CONFIRM_PASSWORD"
		),
		"REQUIRED_FIELDS" => array(
			0 => "EMAIL",
			1 => "NAME",
			2 => "LAST_NAME",
			3 => "PERSONAL_PHONE",
			4 => "PERSONAL_CITY",
			5 => "PASSWORD",
			6 => "CONFIRM_PASSWORD"
		),
		"TYPE" => 'client',
		"SOC_AUTH" => 'Y'
	),
	false
); ?>
<? $APPLICATION->IncludeComponent(
	"aniart:register", 
	"partner",
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
		"USER_PROPERTY" => array(
			0 => "UF_VOEN",
			1 => "UF_TYPE",
			2 => "UF_WHATSAPP",
		),
		"TYPE" => 'partner'
	),
	false
); ?>
<?$APPLICATION->IncludeComponent(
    "aniart:user.restore.password",
    "main",
    Array(
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A"
    )
);?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>