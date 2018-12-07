<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

?>

<script type="text/javascript">
$(document).ready(function(){
    //set component params for ajax_mod
    UserProfileMain.setController('<?=$component->getPath()?>/ajax.php');
    UserProfileMain.setTemplate('<?=$component->getTemplateName()?>');
    UserProfileMain.setParams('<?=json_encode($arParams)?>');
    UserProfileMain.setChildren('<?=count($arResult['UF_CHILDREN'])?>');
});
</script>