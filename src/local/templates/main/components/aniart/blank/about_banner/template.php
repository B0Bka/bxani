<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();?>

<!-- Баннеры -->

<?if(!empty($arResult)):?>
<div class="about">
    
<?foreach($arResult as $item):?>
    <?=$item['html']?>
<?endforeach;?>

</div>
<?endif;?>
<!-- Конец Баннеры -->

