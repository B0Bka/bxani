<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

if(!CModule::IncludeModule("catalog"))
	return;

//список инфоблоков
$rsIBlocks = CIblock::GetList(array("NAME"=>"ASC"));
while($tmp = $rsIBlocks->GetNext())
{
	$arIBlock[$tmp["ID"]] = $tmp["NAME"];
}

//Типы фильтров
$arFilterTypes = array(
	".default"		=>	"Стандартный фильтр для работы со свойствами типов: список, число, привязка к элементам инфоблоков, привязка к секциям инфоблоков",
);

//список свойств выбранного инфоблока
if((int)$arCurrentValues["IBLOCK_ID"]>0)
{
	$arProperties = array();
	$rsProperties = CIBlockProperty::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("IBLOCK_ID"=>(int)$arCurrentValues["IBLOCK_ID"]));
	while($arProperty = $rsProperties->Fetch()){
		$arListProperties[$arProperty["ID"]] = $arProperty["NAME"];
		$arProperties[$arProperty["ID"]] = $arProperty;
	}

	//находим свойства торговых предложений
	if($arCurrentValues["OFFERS_EXIST"] == "Y")
	{
		$rsCatalog = CCatalog::GetList(array(), array("PRODUCT_IBLOCK_ID" => (int)$arCurrentValues["IBLOCK_ID"]));
		if($arCatalog = $rsCatalog->Fetch())
		{
			$OffersIBlockID = $arCatalog["IBLOCK_ID"];
			$rsProperties = CIBlockProperty::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("IBLOCK_ID" => (int)$arCatalog["IBLOCK_ID"]));
			while($arProperty = $rsProperties->Fetch()){
				$arProperty["IS_OFFER"] = true;
				$arListOffersProperties[$arProperty["ID"]] = $arProperty["NAME"];
				$arProperties[$arProperty["ID"]] = $arProperty;
			}
		}
	}
	
}

$arComponentParameters = array(
	"GROUPS" => array(
		"BASE" => array(
			"NAME" => "Параметры"
		)
	),
	"PARAMETERS" => array(
		"IBLOCK_ID" => array(
			"PARENT"	=> "BASE",
			"NAME"		=> "Инфоблок со свойствами для фильтра",
			"TYPE"		=> "LIST",
			"VALUES"	=> $arIBlock,
			"REFRESH"	=> "Y"
		),
		
		"FILTER_NAME"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> "Название request-параметра для фильтра",
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "filter"
		),
		
		"REQUEST_PAGE_URL"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> "Страница на которую отсылается запрос",
			"TYPE"		=> "STRING",
			"DEFAULT"	=> ""
		),
		
		"SEF_CONTROLLER" => array(
			"PARENT"	=> "BASE",
			"NAME"		=> "Класс-контроллер для ЧПУ",
			"TYPE"		=> "STRING",
			"DEFUALT"	=> ""
		),

		"CACHE_TIME"  =>  Array("DEFAULT"=>360000),
	),
);

//Если для данного ифноблока есть инфоблок с торговыми предложениями, спрашиваем пользователя, хочет ли он дополнительно фильтровать по свойствам торговых предложений
if((int)$arCurrentValues["IBLOCK_ID"]>0)
{
	$rsCatalog = CCatalog::GetList(array(), array("PRODUCT_IBLOCK_ID" => (int)$arCurrentValues["IBLOCK_ID"]));
	if($arCatalog = $rsCatalog->Fetch())
	{
		$arComponentParameters["PARAMETERS"]["OFFERS_EXIST"] = array(
				"PARENT"	=> "BASE",
				"NAME"		=> "Дополнительно использовать фильтр по свойствам торговых предложений этого инфоблока",
				"TYPE"		=> "CHECKBOX",
				"DEFAULT"	=> "N",
				"REFRESH"	=> "Y"
		);
	}
}

if(!empty($arProperties))
{
	//список свойств инфоблока
	if(!empty($arListProperties)){
		$arComponentParameters["PARAMETERS"]["IBLOCK_PROPERTIES"] = array(
			"PARENT"	=> "BASE",
			"NAME"		=> "Свойства, которые будут участвовать в фильтре",
			"TYPE"		=> "LIST",
			"VALUES"	=> $arListProperties,
			"SIZE"		=> "7",
			"MULTIPLE"	=> "Y",
			"REFRESH"	=> "Y"
		);
	}
	
	//список свойств торговых предложений инфоблока
	if($arCurrentValues["OFFERS_EXIST"] == "Y")
	{
		if(!empty($arListOffersProperties)){
			$arComponentParameters["PARAMETERS"]["OFFERS_PROPERTIES"] = array(
					"PARENT"	=> "BASE",
					"NAME"		=> "Свойства торговых предложений, которые будут участвовать в фильтре",
					"TYPE"		=> "LIST",
					"VALUES"	=> $arListOffersProperties,
					"SIZE"		=> "7",
					"MULTIPLE"	=> "Y",
					"REFRESH"	=> "Y",
			);
		}
		
		//инфоблок для элементов которого, собственно и будет происходить фильтрация
		$arComponentParameters["PARAMETERS"]["MAIN_IBLOCK"] = array(
				"PARENT"	=> "BASE",
				"NAME"		=> "Инфоблок, к которому будет применяться фильтр",
				"TYPE"		=> "LIST",
				"VALUES"	=> array($arCurrentVaslues["IBLOCK_ID"] => $arIBlock[$arCurrentValues["IBLOCK_ID"]], $OffersIBlockID => $arIBlock[$OffersIBlockID]),
				"SIZE"		=> "2",
		);
	}
	
	//виртуальные свойства(для обьединения нескольких свойств, построения своей логики и т.д)
	$arComponentParameters["PARAMETERS"]["VIRTUAL_PROPERTIES"] = array(
		"PARENT"			=> "BASE", 
		"NAME"				=> "Виртуальные свойства",
		"TYPE"				=> "STRING",
		"MULTIPLE"			=> "Y",
		"ADDITIONAL_VALUES"	=> "Y",
		"REFRESH"			=> "Y"
	);

	$arAllProperties = array();
	if(!empty($arCurrentValues["IBLOCK_PROPERTIES"]) && !empty($arCurrentValues["IBLOCK_PROPERTIES"][0]))
		$arAllProperties = $arCurrentValues["IBLOCK_PROPERTIES"];
	if(!empty($arCurrentValues["OFFERS_PROPERTIES"]) && !empty($arCurrentValues["OFFERS_PROPERTIES"][0]))
		$arAllProperties = array_merge($arAllProperties, $arCurrentValues["OFFERS_PROPERTIES"]);
	
	if(!empty($arCurrentValues["VIRTUAL_PROPERTIES"][0]))
	{
		foreach($arCurrentValues["VIRTUAL_PROPERTIES"] as $value)
		{
			$arAllProperties[] = $value;
			$arProperties[$value] = array(
				"ID"				=> $value,
				"CODE"				=> $value,
				"NAME"				=> $value,
				"PROPERTY_TYPE"		=> "VIRTUAL",
				"IS_VIRTUAL"		=> true,
			);
		}
	}
	
	//echo "<pre>";
	//var_dump($arProperties);die;
	//echo "</pre>";
	
	if(!empty($arAllProperties))
	{
		foreach($arAllProperties as $index => $prop_id)
		{

			$key		= "PROPERTY_".$prop_id;
			$group_name	= $arProperties[$prop_id]["NAME"];

			if($arProperties[$prop_id]["IS_OFFER"] === true){
				$key		= "PROPERTY_OFFER_".$prop_id;
				$group_name	= "Торговое предложение: ".$group_name;
			}
			elseif($arProperties[$prop_id]["IS_VIRTUAL"] === true){
				$key		= "PROPERTY_VIRTUAL_".$prop_id;
				$group_name	= "Виртуальное свойство: ".$group_name;
			}
			
			
			$arComponentParameters["GROUPS"][$key] = array(
				"NAME"	=> $group_name,
				"SORT"	=> 101+$index,
			);
			//Заголовок
			$arComponentParameters["PARAMETERS"][$key."_TITLE"] = array(
				"PARENT"			=> $key,
				"NAME"				=> "Заголовок",
				"TYPE"				=> "STRING",
				"DEFAULT"			=> $arProperties[$prop_id]["NAME"],
			);
			//Тип фильтра
			$arComponentParameters["PARAMETERS"][$key."_TYPE"] = array(
				"PARENT"			=> $key,
				"NAME"				=> "Тип фильтра",
				"TYPE"				=> "LIST",
				"ADDITIONAL_VALUES"	=> "Y",
				"VALUES"			=> $arFilterTypes,
			);
			//Сортировка
			$arComponentParameters["PARAMETERS"][$key."_SORT"] = array(
				"PARENT"		=> $key,
				"NAME"			=> "Сортировка",
				"TYPE"			=> "STRING",
				"DEFAULT"		=> "100",
			);
			//Шаблон
			$arComponentParameters["PARAMETERS"][$key."_TEMPLATE"] = array(
				"PARENT"		=> $key,
				"NAME"			=> "Шаблон",
				"TYPE"			=> "STRING",
				"DEFAULT"		=> ""
			);
			//Мультивыбор
			$arComponentParameters["PARAMETERS"][$key."_MULTIPLE"] = array(
							"PARENT"			=> $key,
							"NAME"				=> "Мультивыбор",
							"TYPE"				=> "CHECKBOX",
							"DEFAULT"			=> "Y",
			);
			//Отображения к-ва элементов по этому свойству
			$arComponentParameters["PARAMETERS"][$key."_SHOWCOUNT"] = array(
							"PARENT"			=> $key,
							"NAME"				=> "Отображать количество",
							"TYPE"				=> "CHECKBOX",
							"DEFAULT"			=> "Y",
			);
		}
	}
}

?>