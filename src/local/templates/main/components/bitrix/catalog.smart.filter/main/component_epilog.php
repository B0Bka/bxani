<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION;
CJSCore::Init(array('fx', 'popup'));

if (isset($templateData['TEMPLATE_THEME']))
{
	$APPLICATION->SetAdditionalCSS($templateData['TEMPLATE_THEME']);
}
if(Bitrix\Main\Loader::includeModule('seo.filter'))
{
    $seo = new \Seo\Filter\Meta($arParams['IBLOCK_ID'], $arResult['ITEMS'], $arParams['SECTION_ID']);
    $seo->setCanonical();
    $seo->setMeta();
    $h1 = $seo->getH1();
}
?>