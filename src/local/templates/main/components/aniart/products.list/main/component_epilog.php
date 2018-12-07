<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<script type="text/javascript">
$(document).ready(function() {
    //set component params
    CatalogProductsListMain.setParams('<?=$component->getSignedComponentParams()?>');
    App.Catalog.setPagination(
        <?=CUtil::PhpToJSObject($arResult['PAGINATION'])?>
    );

    //init 
//    CatalogProductsListMain.initAnimOnScroll();
    //get pager data
    CatalogProductsListMain.getPager();
    <?
    if(empty($arResult['PRODUCTS'])):
    ?>
        $('.sort-link').hide();
    <?else:
        $id = $arResult['SECTION_ID'] > 0 ? $arResult['SECTION_ID'] : $arParams['FILTER']['PROPERTY_TREND'];
    ?>
        $('.sort-link').show().attr('href', '/sort/?id=<?=$id?>&page=<?=$arResult['PAGINATION']['NavPageNomer']?>&type=<?=$arParams['TYPE']?>');
    <?endif;?>
});
</script>
