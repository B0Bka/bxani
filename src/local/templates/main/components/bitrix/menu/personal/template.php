<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<?if (!empty($arResult)):?>
<div id="menu_footer_personal" class="one-footer-menu">
    <div class="footer-tit">
        <?=i18n('PERSONAL')?> <i class="fa fa-angle-down" aria-hidden="true"></i>
    </div>
    <ul>
    <?foreach($arResult as $item):?>
        <li>
            <?if($item['LINK'] == '/personal/#1'):?>
                <span hashstring="footer_basket" hashtype="content">&nbsp</span>
            <?else:?>
                <a
                    href="<?=$item['LINK']?>"
                    <?=(empty($item['PARAMS']['DATA'])?'':$item['PARAMS']['DATA'])?>
                ><?=$item['TEXT']?></a>
            <?endif?>
        </li>
    <?endforeach?>
    </ul>
</div>
<?endif;?>
