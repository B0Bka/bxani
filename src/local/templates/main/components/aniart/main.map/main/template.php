<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<div class="main-map">
    <div class="block-map">
        <?if(!empty($arResult['CATALOG'])):?>
            <div class="map-block">
                <div class="map-title"><?=i18n('CATALOG_PAGES','sitemap')?></div>
                <?foreach ($arResult['CATALOG'] as $block):?>
                    <ul class="ul-treefree ul-dropfree">
                        <?foreach($block as $group):?>
                        <li>
                            <a href="<?=$group['URL']?>"><?=$group['NAME']?></a>
                            <?if(!empty($group['ITEMS'])):?>
                                <ul style="display: none">
                                    <?foreach($group['ITEMS'] as $item):?>
                                        <li> <a href="<?=$item['URL']?>"><?=$item['NAME']?></a></li>
                                    <?endforeach?>
                                </ul>
                            <?endif;?>
                        </li>
                        <?endforeach;?>
                    </ul>
                <?endforeach?>
            </div>
        <?endif;?>
    </div>
    <div class="block-map">
         <?if(!empty($arResult['PERSONAL'])):?>
            <div class="map-block">
                <div class="map-title"><?=i18n('PERSONAL_PAGES','sitemap')?></div>
                <ul class="ul-treefree ul-dropfree">
                    <?foreach ($arResult['PERSONAL'] as $item):?>
                        <li>
                            <a href="<?=$item['URL']?>"><?=$item['NAME']?></a>
                        </li>
                    <?endforeach?>
                </ul>
            </div>
        <?endif;?>
        <?if(!empty($arResult['CONTENT'])):?>
            <div class="map-block">
                <div class="map-title"><?=i18n('CONTENT_PAGES','sitemap')?></div>
                <ul class="ul-treefree ul-dropfree">
                    <?foreach ($arResult['CONTENT'] as $item):?>
                        <li>
                            <a href="<?=$item['URL']?>"><?=$item['NAME']?></a>
                        </li>
                    <?endforeach?>
                </ul>
            </div>
        <?endif;?>
        </div>
    </div>
</div>

