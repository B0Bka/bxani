<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бренды");?><?$APPLICATION->IncludeComponent(
	"aniart:user.restore.password",
	"",
	Array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>