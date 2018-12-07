<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global $APPLICATION
 * @var array $arResult
 * @var array $arParams
 * @var AniartProductsListComponent $component
 * @var \Aniart\Main\Models\Product[] $products
 */
$component = $this->getComponent();
$promotion  = $arResult['PROMOTION'];
?>

<?if(!empty($arResult)):?>
<div class="one-blog-page">
    <!-- Один пост -->
    <div class="one-post-blog">
        <div class="one-post-blog-in">
            <div class="one-post-descr">
                <div class="one-post-descr-in">
                    <h1 class="one-post-blog-tit">
                        <?=$promotion->getName()?>
                    </h1>
                    <div class="one-post-blog-data">
                       <?=$promotion->getDate()?>
                    </div>
                    <div class="one-post-blog-text">
                       <?=$promotion->getDetailText()?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Конец Один пост -->
    <?$this->SetViewTarget('blog_detail');?>
        <div class="new-hero">
            <img
                src="<?=$promotion->getDetailPicture()?>"
                alt="<?=$promotion->getName()?>"
                title="<?=$promotion->getName()?>"
            />
        </div>
    <?$this->EndViewTarget();?>
</div>
<?endif;?>

