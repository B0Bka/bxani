<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//new \dBug($arResult['PRODUCTS']);
//new \dBug($arResult['PAGINATION']);

?>

<script type="text/javascript">
$(document).ready(function() {
    //set component params
    CatalogProductsListNovelty.setParams('<?=$component->getSignedComponentParams()?>');
});
</script>
