<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

<ul>

<?

//dBug($arResult);

$previousLevel = 0;
foreach($arResult as $arItem):?>

	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?if ($arItem["IS_PARENT"]):?>

		<?if ($arItem["DEPTH_LEVEL"] == 1):?>
			<li class="<?=($arItem['SELECTED'] ? 'active open' : '')?>">
                <?/*<span></span>*/?>
                <a href="<?=$arItem["LINK"]?>" >
                    <?=$arItem["TEXT"]?>
                    <?/*<i class="fa fa-angle-down" aria-hidden="true"></i>*/?>
                </a>
				<ul>
		<?else:?>
			<li class="<?=($arItem['SELECTED'] ? 'active open' : '')?>">
                <a href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>
				<ul>
		<?endif?>

	<?else:?>

		<?if ($arItem["PERMISSION"] > "D"):?>

			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<li class="<?=($arItem['SELECTED'] ? 'active' : '')?>">
                    <a href="<?=$arItem["LINK"]?>">
                        <?/*<span></span>*/?>
                        <?=$arItem["TEXT"]?>
                    </a>
			<?else:?>
				<li class="<?=($arItem['SELECTED'] ? 'active' : '')?>">
                    <a href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>
			<?endif?>

		<?endif?>

	<?endif?>

	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>

</ul>
<?endif?>

<script type="text/javascript">
/*$(document).ready(function() {
    $('.side-menu').on('click', 'li span', function(){
        //$('.side-menu li').removeClass('open');
        $(this).parent().toggleClass('open');
    });
});*/
</script>