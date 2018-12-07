<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();?>

<!-- Баннеры -->
<?if(!empty($arResult)):?>

<div class="two-banner">
    <div class="one-home-slide">
<?foreach($arResult as $item):?>
    
        <?=$item['html']?>
    
<?endforeach;?>
    </div>
</div>

<?endif;?>
<!-- Конец Баннеры -->