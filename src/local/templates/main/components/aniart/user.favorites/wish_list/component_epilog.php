<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//new \dBug($arResult['PRODUCTS']);
//new \dBug($arResult['PAGINATION']);

?>

<script type="text/javascript">
$(document).ready(function() {
    //set component params
    CatalogProductListMain.setParams('<?=$component->getSignedComponentParams()?>');
    CatalogProductListMain.setPagination(
        <?=CUtil::PhpToJSObject($arResult['PAGINATION'])?>
    );

    //init 
   // CatalogProductListMain.initAnimOnScroll();
    //get pager data
    CatalogProductListMain.getPager();
});
</script>
