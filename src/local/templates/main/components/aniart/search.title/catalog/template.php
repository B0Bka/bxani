<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true); ?>
<?
if ($arParams["SHOW_INPUT"] !== "N"):?>
    <div class="search-pop" >
        <div class="search-form" id="<?=$CONTAINER_ID?>">
            <div class="search-top" >
                <form action="<? echo $arResult["FORM_ACTION"] ?>">
                    <input id="<? echo $INPUT_ID ?>" type="text" name="q" value="<?=ltrim($_REQUEST["q"])?>" placeholder="<?=i18n('SEARCH_PLACEHOLDER')?>"
                           class="search-inp" autocomplete="off"/>&nbsp;
                    <input name="s" type="submit" class="search-bt" value="<?//= GetMessage("CT_BST_SEARCH_BUTTON"); ?>"/>
                </form>
            </div>
        </div>
    </div>
<? endif ?>