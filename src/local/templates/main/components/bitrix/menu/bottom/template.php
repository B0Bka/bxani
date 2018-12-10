<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
    <div class="one-footer-menu">
        <? foreach ($arResult['COLUMNS'] as $col):?>
            <ul>
                <?foreach($col as $item):?>
                    <li <?=containStr($item['LINK'], 'brands') ? 'class="brand"' : '';?>>
                        <a href="<?=$item['LINK']?>"><?=$item['TEXT']?></a>
                    </li>
                <?endforeach?>
            </ul>
        <?endforeach;?>
    </div>
<?endif;?>
