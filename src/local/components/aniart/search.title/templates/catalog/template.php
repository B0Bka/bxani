<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true); ?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if (strlen($INPUT_ID) <= 0) {
    $INPUT_ID = "title-search-input";
}
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if (strlen($CONTAINER_ID) <= 0) {
    $CONTAINER_ID = "title-search";
}
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if ($arParams["SHOW_INPUT"] !== "N"):?>
    <div class="search-pop" >

        <div class="close-search">
            <svg width="29" height="29" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29 29"><defs><style>.cls-11{fill:none;stroke:#4d4d4d;stroke-miterlimit:10;}</style></defs><g id="Слой_2" data-name="Слой 2"><g id="m_Поиск_01" data-name="m_Поиск 01"><circle class="cls-11" cx="14.5" cy="14.5" r="14"/><line class="cls-11" x1="9.9" y1="9.9" x2="19.1" y2="19.1"/><line class="cls-11" x1="9.9" y1="19.1" x2="19.1" y2="9.9"/></g></g></svg>
        </div>
        <div class="search-form" id="<?=$CONTAINER_ID?>">
            <div class="search-top" >
                <form action="<? echo $arResult["FORM_ACTION"] ?>">
                    <input id="<? echo $INPUT_ID ?>" type="text" name="q" value="<?=ltrim($_REQUEST["q"])?>" placeholder="Что ищем?"
                           class="search-inp" autocomplete="off"/>&nbsp;
                    <input name="s" type="submit" class="search-bt" value="<?= GetMessage("CT_BST_SEARCH_BUTTON"); ?>"/>
                </form>
                <div class="err">Введите запрос</div>
            </div>
        </div>
    </div>

<? endif ?>
<script>
    BX.ready(function () {
        new JCTitleSearch({
            'AJAX_PAGE': '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
            'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
            'INPUT_ID': '<?echo $INPUT_ID?>',
            'MIN_QUERY_LEN': 2
        });
    });
</script>