<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

?>

<script type="text/javascript">
$(document).ready(function(){
    //set component params for ajax_mod
    SubscriptionMain.setController('<?=$component->getPath()?>/ajax.php');
    SubscriptionMain.setTemplate('<?=$component->getTemplateName()?>');
    SubscriptionMain.setParams('<?=json_encode($arParams)?>');
});
</script>