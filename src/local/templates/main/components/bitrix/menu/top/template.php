<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<?if (!empty($arResult)):?>
    <div class="one-footer-menu">
        <ul>
            <?foreach($arResult as $item):?>
                <li><a href="<?=$item['LINK']?>"><?=$item['TEXT']?></a></li>
            <?endforeach?>
        </ul>
    </div>
<?endif;?>
