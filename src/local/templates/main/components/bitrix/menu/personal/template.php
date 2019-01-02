<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
    <div class="one-footer-menu">
        <ul>
        <?foreach($arResult as $item):
           if($item["PERMISSION"] > "D"):?>

            <li>
                    <a href="<?=$item['LINK']?>" <?=$item["SELECTED"] ? 'class="selected"': ""?>
                        <?=(empty($item['PARAMS']['DATA'])?'':$item['PARAMS']['DATA'])?>
                    ><?=$item['TEXT']?></a>

            </li>
            <?
            endif;
        endforeach?>
        </ul>
    </div>
<?endif;?>
