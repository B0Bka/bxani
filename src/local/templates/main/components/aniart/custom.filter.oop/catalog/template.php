<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
/**
 * @var array $arResult
 * @var array $arParams
 * @var CustomFilter $filter
 */
$filter = $arResult["FILTER"];
$selectedValues = $filter->GetSelectedValues();
$filterProperties = $filter->GetProperties();

/**
 * @var \Aniart\Main\Seo\CustomFilterSEFController $sefController
 */
$sefController  = $filter->GetSEFController();
$sectionQueryUrl = getLinkWithQueryString($arParams['REQUEST_PAGE_URL']);
$selectedValuesCount = $selectedValues->SelectedAllValuesCount();
?>

<?if($_SERVER["DOCUMENT_URI"] != NASHY_TRENDY_PATH && app()->getDeviceType() != 'mobile'){?>
<div id="custom_filter" class="side-menu filt-menu">
    <span class="menu-name"><?=i18n('FILTER')?></span>
    <?if(!empty($_REQUEST['filter'])){?>
            <div class="reset-filt">
                <input
                        id="custom_filter_reset"
                        type="button"
                        value="<?=i18n('RESET_FILTER')?>"
                        onclick="App.CatalogFilter.resetFilter('<?=isset($_GET['sort']) ? 'sort=' . $_GET['sort'] : ''?>')"
                >
            </div>
        <div id="filters_active">
            <ul>
            <?foreach ($_REQUEST['filter'] as $prop_key=>$property_group){
                foreach ($property_group as $key=>$value){?>
                <li class="one-active-filt">
                    <label for="label_<?= $prop_key ?>_<?= $value ?>">
                        <span>
                            <?($filterProperties[$prop_key]->GetData()["VALUES"][$value]["CODE"]) ?
	                            $data_code = $filterProperties[$prop_key]->GetData()["VALUES"][$value]["CODE"] :
	                            $data_code = $filterProperties[$prop_key]->GetData()["VALUES"][$value]["ID"]?>
                            <?=$selectedValues->GetSelectedFiltersNames($prop_key, $value)?>
                        </span>
                    <input type="checkbox"
                           data-url="<?=$sefController->getPropertyLink($prop_key, $value)?>"
                           data-code="<?=$data_code?>"
                           data-propcode="<?=strtolower($arParams["PROPERTY_".$prop_key."_CODE"])?>"
                           value="<?=$value?>"
                           data-property_id="<?=$prop_key?>"
                           id="label_<?= $prop_key ?>_<?= $value ?>"
                           onchange="App.CatalogFilter.submitFilter(this)"
                    >
                    </label>
                </li>
            <?}
            }?>
            </ul>
        </div>
    <?}?>
    <ul id="custom_filter_properties">
    <?if($filter->PropertiesCount() > 0):?>
     <?/*   новая правка к фильтрам. https://i.imgur.com/DSfgmi5.png
        Слева оставляем 2 основных. размер, цвет. Остальные выходят после клика на еще фильтры. NB-358*/?>
	    <?foreach ($filter->GetProperties() as $obProperty):?>
		    <?if($obProperty->getID() == PROP_MIN_PRICE_ID /*добавить мобайл проверку */ && app()->getDeviceType() == 'mobile'){
			    $obProperty->setParam('TEMPLATE', ['price']);
			    echo($obProperty->GetHtml('price'));
		    }elseif($obProperty->getID() == PROP_SIZE_ID){
				$obProperty->setParam('TEMPLATE', ['size']);
			    echo($obProperty->GetHtml('size'));
			}?>
		    <?if($obProperty->getID() != PROP_COLOR_ID){continue;}?>
            <?=$obProperty->GetHtml('main');?>

        <?endforeach?>
        <li class="more-properties <?=($filter->getMorePropertiesOpen(FILTER_MORE_PROPS)) ? 'opened' : ''?>">
            <span class="toggle-open-js"></span>
            <a href="javascript:void(0);" class="bold-opened">
                Еще фильтры:
                <i class="fa fa-angle-down" aria-hidden="true"></i>
            </a>
<!--            --><?//=$filter->getOpenFilters()?>
            <ul class="<?=($filter->getMorePropertiesOpen(FILTER_MORE_PROPS)) ? 'filt-opened' : ''?>">
	        <?foreach ($filter->GetProperties() as $obProperty):?>
		        <?if($obProperty->getID() == PROP_SIZE_ID || $obProperty->getID() == PROP_COLOR_ID) {
			        continue;
		        } else { ?>
                    <?= $obProperty->GetHtml('main'); ?>
			        <?if($obProperty->getID() == PROP_MIN_PRICE_ID /*добавить мобайл проверку */ && app()->getDeviceType() != 'mobile'){
				        $obProperty->setParam('TEMPLATE', ['price']);
				        echo($obProperty->GetHtml('price'));
			        }?>
                <?}?>
	        <?endforeach?>

            </ul>
        </li>
    <?endif?>
            
    </ul>

    <div class="dane-filt">
        <input 
            id="custom_filter_active" 
            type="button" 
            value="<?=i18n('ACCEPT')?>" 
            onclick="App.CatalogFilter.submitFilter()" 
        >
    </div>

</div>
<?}?>

<script>
    <?
    $jsFilter = CUtil::PhpToJSObject($arParams['MORE_PROPERTY']);
    ?>
    (function(){
        var $filter = $('#custom_filter');
        App.CatalogFilter = new App.CustomFilter($filter, {
            url: '<?=$arParams['REQUEST_PAGE_URL']?>',
            filter: <?=$jsFilter?>,
            lang: '<?=i18n()->lang()?>',
            $properties: $filter.find('#custom_filter_properties'),

        });
        App.CatalogFilter.onClickItemEvent($filter);

    })();
</script>


