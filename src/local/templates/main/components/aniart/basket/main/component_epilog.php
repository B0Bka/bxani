<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

?>

<script type="text/javascript">
$(document).ready(function() {
    //set component params for ajax_mod
    BasketMain.setController('<?=$component->getPath()?>/ajax.php');
    BasketMain.setTemplate('<?=$component->getTemplateName()?>');
    BasketMain.setParams('<?=json_encode($arParams)?>');
});
</script>