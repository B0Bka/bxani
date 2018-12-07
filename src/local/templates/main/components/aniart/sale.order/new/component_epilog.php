<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();
global $APPLICATION;
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/lib/inputmask.bundle.js');
?>
<script>
    $(document).ready(function () {
        setTimeout(function () {
            jQuery(".input-phone").inputmask("+38 (999) 999-99-99");//ногда не инициализируется?
        }, 500)
    })
</script>
