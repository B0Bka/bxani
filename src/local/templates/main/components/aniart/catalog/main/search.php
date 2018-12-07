<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var AniartCatalogComponent $component
 * @var \Aniart\Main\Models\ProductSection $section
 */
$component = $this->getComponent();
$section = $arResult['SECTION'];
$APPLICATION->SetTitle(i18n('SEARCH_TITLE'));
$APPLICATION->AddChainItem(i18n('SEARCH_TITLE'));

$arResult['SEF_CONTROLLER_CLASS']::bindFilteredPropsToUrl(
    $arResult['SEF_PAGE_URL'],
    $section->getId(),
    i18n()->lang()
);
if (registry()->isExists('sef_redirect')) {//component.php
    LocalRedirect(registry('sef_redirect'));
}
global $USER;
$arGroups = CUser::GetUserGroup($USER->GetID());
?>
<?$searchFilter = $APPLICATION->IncludeComponent(
    'aniart:catalog.search',
    'main',
    [
        'CACHE_TYPE' => 'N',
        'CACHE_TIME' => 36000,
        'FILTER' => $arSearchFilter,
    ],
    $component,
    ['HIDE_ICONS' => 'Y']
);
if($searchFilter) include 'section.php';
//    else echo i18n('EMPTY_SEARCH_RESULT');?>

<button onclick="topFunction()" id="myBtn" title="Go to top"><span>&lsaquo;</span></button>
<script>
/*google remarketing*/
var google_tag_params = {
    dynx_itemid: '',
    dynx_pagetype: 'searchresults',
    dynx_totalvalue: ''
};
</script>