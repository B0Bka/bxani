<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 */
if(isset($arResult['TEXT'])):
    echo $arResult['TEXT'];
?>
    <script>
        $( document ).ready(function() {
            $('#personal_menu ul li[data-id=2] a').click();
        });
    </script>
<?endif;?>
