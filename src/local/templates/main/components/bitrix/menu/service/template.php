<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<?if (!empty($arResult)):?>
<div class="one-footer-menu">
    <div class="footer-tit">
        <?=i18n('SERVICE')?> <i class="fa fa-angle-down" aria-hidden="true"></i>
    </div>
    <ul>
    <?foreach($arResult as $item):?>
        <li><a href="<?=$item['LINK']?>"><?=$item['TEXT']?></a></li>
    <?endforeach?>
    </ul>
</div>
<?endif;?>
