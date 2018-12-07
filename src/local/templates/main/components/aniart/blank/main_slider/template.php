<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();

//dBug($arResult);
?>

<?if(!empty($arResult)):?>
<div class="home-slider">
    <div class="homeslider">
    <?foreach($arResult as $item):?>
        <div class="one-home-slide">
            <a href="#">
                <picture>
                    <?=$item['html']?>
                </picture>
            </a>
        </div>
    <?endforeach;?>
    </div>
    <div class="mini-slider">
        <div id="bx-pager">
        <?foreach($arResult as $key=>$item):?>
            <a data-slide-index="<?=$key?>" href="">
                <span>
                    <?=$item['NAME']?>
                </span>
            </a>
        <?endforeach;?>
        </div>
    </div>
</div>
<?endif;?>
