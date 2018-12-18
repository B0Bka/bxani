<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle(i18n('CHANGE_PASSWORD', 'change'));
?>
<?$APPLICATION->IncludeComponent(
    "aniart:change.password",
    "main",
    Array(
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A"
    )
);?>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>