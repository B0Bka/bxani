<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карьера");?>
<?
if(Bitrix\Main\Loader::includeModule('seo.filter'))
{
    $facetSmartFilter = new Seo\Filter\Xml(1, 1);
    $filterMap[418] = $facetSmartFilter->genSectionFilterSitemap();
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>