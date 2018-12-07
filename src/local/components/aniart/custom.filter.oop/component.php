<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
//подключаем модель фильтра и доп. функции
include_once "include.php";

$arParams["IBLOCK_ID"]		= (int)$arParams["IBLOCK_ID"];
$arParams["OFFERS_EXIST"]	= $arParams["OFFERS_EXIST"] == "Y";
$arParams["MAIN_IBLOCK"]	= $arParams["MAIN_IBLOCK"]?$arParams["MAIN_IBLOCK"]:$arParams["IBLOCK_ID"];
$arParams["FILTER_NAME"]	= $arParams["FILTER_NAME"]?$arParams["FILTER_NAME"]:"filter";
$arParams['GET_FILTER']		= $arParams['GET_FILTER'] == 'Y';

if(!CModule::IncludeModule("iblock")){
	ShowError("Модуль инфоблоков не установлен");die;
}

if($arParams["OFFERS_EXIST"] === true && !CModule::IncludeModule("catalog")){
	ShowError("Модуль торгового каталога не установлен");die;
}
elseif($arParams["OFFERS_EXIST"] === true){
	$rsCatalog = CCatalog::GetList(array(), array("PRODUCT_IBLOCK_ID" => $arParams["IBLOCK_ID"]));
	if($arCatalog = $rsCatalog->Fetch())
	{
		$arParams["OFFERS_IBLOCK_ID"]	= $arCatalog["IBLOCK_ID"];
		$arParams["OFFERS_PROPERTY_ID"]	= $arCatalog["SKU_PROPERTY_ID"];

	}
}

if(!is_array($arParams['PROPERTIES_META_DATA'])){
	$arParams['PROPERTIES_META_DATA'] = [];
}
//Создаем экземпляр фильтра и добавляем в него фильтруемые свойства
$Filter = CustomFilters::Add($arParams["FILTER_NAME"], array(
	"MAIN_IBLOCK"			=> $arParams["MAIN_IBLOCK"],
	"PRODUCTS_IBLOCK_ID"	=> $arParams['IBLOCK_ID'],
	"OFFERS_IBLOCK_ID"		=> $arParams["OFFERS_IBLOCK_ID"],
	"OFFERS_PROPERTY_ID"	=> $arParams["OFFERS_PROPERTY_ID"],
	"SECTION_ID"			=> $arParams["SECTION_ID"],
	"INCLUDE_SUBSECTIONS" 	=> $arParams["INCLUDE_SUBSECTIONS"],
	"MORE_PROPERTY"			=> $arParams["MORE_PROPERTY"],
	"SEF_CONTROLLER"		=> $arParams["SEF_CONTROLLER"],
	"OTHER_PROPERTIES"		=> $arParams["OTHER_PROPERTIES"],
	"LANG"		            => $arParams["LANG"],
));

/**
 * @var CustomFilter $Filter
 */
$Filter->AddProperties($arParams["IBLOCK_PROPERTIES"])
	->AddProperties($arParams["OFFERS_PROPERTIES"], "offer")
	->AddProperties($arParams["VIRTUAL_PROPERTIES"], "virtual")
	->ObtainPropertiesData()
	->SetAdditionalPropsMetaData($arParams['PROPERTIES_META_DATA']);


//Парсим параметры компонента и добавляем пользовательские параметры к фильтруемым свойствам
foreach($arParams as $key => $value)
{
	if(is_string($value)){
		$value		= trim($value);
	}
	$subkeys	= explode("_", $key);

	if($subkeys[0] == "PROPERTY")
	{
		$propertyID = is_numeric($subkeys[1])?$subkeys[1]:$subkeys[2];
		$paramName	= end($subkeys);
        if(!empty($propertyID)){
            $property = $Filter->GetProperty($propertyID);
            if($paramName == 'DATA'){
                $property->SetData($value);
            }
            else{
                $property->SetParam($paramName, $value);
            }
        }
	}
}

//Сортируем свойства
$Filter->SortProperties();
//Если задан режим ЧПУ - подключаем обработчик
if(!empty($arParams['SEF_CONTROLLER']) && class_exists($arParams['SEF_CONTROLLER'])){
	$Filter->setSEFController(new $arParams['SEF_CONTROLLER']($Filter));
}
//Кнопка на панели для добавления СЕО
global $USER;
$arGroups = CUser::GetUserGroup($USER->GetID());
if((in_array(6, $arGroups) || in_array(1, $arGroups)))
{
	$APPLICATION->AddPanelButton(
		Array(
			"ID" => 'costum_meta', //определяет уникальность кнопки
			"TEXT" => "Метаданные",
			"TYPE" => "", //BIG - большая кнопка, иначе маленькая
			"MAIN_SORT" => 2000, //индекс сортировки для групп кнопок
			"SORT" => 20, //сортировка внутри группы
			"HREF" => "javascript:App.Panel.addMeta({'url':'".$APPLICATION->GetCurPage()."'})", //или javascript:MyJSFunction())
			"ICON" => "bx-panel-seo-icon", //название CSS-класса с иконкой кнопки
			"SRC" => "",
			"ALT" => "Текст всплывающей подсказки", //старый вариант
			"HINT" => array( //тултип кнопки
				"TITLE" => "META",
				"TEXT" => "Добавление мета тегов" //HTML допускается
			),

			),
		$bReplace = false //заменить существующую кнопку?
	);
}
//Включаем обработку свойств( логика + буферизация HTML)
$Filter->Build();
if($Filter->inSEFMode()){
	global $LANG;
	$Filter->getSEFController()->setRootUrl($arParams['REQUEST_PAGE_URL']);
	$Filter->getSEFController()->generatePropertiesLinks();
}
if($arParams['GET_FILTER']){
	return $Filter;
}
else{
	$arResult["FILTER"] = $Filter;
	ob_start();
		$this->IncludeComponentTemplate();
		$templateHtml = ob_get_contents();
	ob_end_clean();
	if($Filter->inSEFMode()){
		$templateHtml = $Filter->getSEFController()->insertSEFLinks($templateHtml);
	}
	if(!empty($arParams['ADD_TO_VIEW_CONTENT'])){
		global $APPLICATION;
		$APPLICATION->AddViewContent($arParams['ADD_TO_VIEW_CONTENT'], $templateHtml);
	}
	else{
		echo $templateHtml;
	}

    return $Filter;
	//return $Filter->ObtainFullBitrixFilter();
}