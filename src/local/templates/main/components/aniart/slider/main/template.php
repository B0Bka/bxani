<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//dBug($arParams);
//dBug($arResult);

$slides = $arResult['ITEMS'];
$count = 0;
?>

<?if(!empty($slides)):?>
    <div class="main-sl-loader">
        <div class="main-sl-loader-in">

        </div>
    </div>
<div class="home-slider">

    <div class="homeslider">
    <?foreach($slides as $slide):
        $link = $slide->getLink();
        ?>
        <div class="one-home-slide">
            <?=(!empty($link)) ? '<a href="'.$link.'">' : ''?>
                <picture>
                    <source srcset="<?=$slide->getPictureXS()?>" media="(min-width: 0px) and (max-width: 479px)">
                    <source srcset="<?=$slide->getPictureS()?>" media="(min-width: 480px) and (max-width: 767px)">
                    <source srcset="<?=$slide->getPictureM()?>" media="(min-width: 768px) and (max-width: 1023px)">
                    <source srcset="<?=$slide->getPictureL()?>" media="(min-width: 1024px) and (max-width: 1279px)">
                    <img src="<?=$slide->getPictureXL()?>" srcset="<?=$slide->getPictureXL()?>" alt="<?=$slide->getName()?> - интернет-магазин Natali Bolgar" title="<?=$slide->getName()?>" class="loaded">
                </picture>
                <?=$slide->getAreaXL()?>
                <?=$slide->getAreaL()?>
                <?=$slide->getAreaM()?>
                <?=$slide->getAreaS()?>
                <?=$slide->getAreaXS()?>
            <?=(!empty($link)) ? '</a>' : ''?>
        </div>
    <?endforeach;?>
    </div>
    <div class="mini-slider">
        <div id="bx-pager">
        <?foreach($slides as $slide):?>
            <a data-slide-index="<?=$count++?>" href="javascript:void(0);">
                <span>
                    <?=$slide->getName()?>
                </span>
            </a>
        <?endforeach;?>
        </div>
    </div>
</div>

<?endif;?>