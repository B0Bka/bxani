<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<?if (!empty($arResult)):?>
<div class="one-footer-menu">
    <div class="footer-tit">
        <?=i18n('COMPANY')?> <i class="fa fa-angle-down" aria-hidden="true"></i>
    </div>
    <ul>
    <?foreach($arResult as $item):?>
        <li>
            <?if($item['LINK'] == '/feedback'):?>
                <a href="javascript:void(0)" onclick="App.Feedback.showFeedBackForm()">
                    <?=$item['TEXT']?>
                </a>
            <?elseif($item['LINK'] == '/publichnaya_oferta/'):?>
                <span hashstring="footer_oferta" hashtype="content">&nbsp</span>
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
