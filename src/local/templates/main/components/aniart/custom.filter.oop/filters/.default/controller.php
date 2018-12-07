<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var CustomFilterProperty $Property
 * @var CustomFilterSelectedValues $SelectedValues
 */
$Property = $arParams["PROPERTY"];
$SelectedValues = $arParams["SELECTED_VALUES"];
$property = "PROPERTY_" . $Property->GetID();

//new \dBug($arParams["PROPERTY"], '', true);
//new \dBug($Property->GetData(), '', true);

switch ($Property->GetData("PROPERTY_TYPE")) {
    case "N":
        //для данного типа достанем максимальное и минимальное значения с учетом ранее установленных фильтров

        //ищем модели
        $arFilter = array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $arParams["MAIN_IBLOCK"],
        );

        $BitrixFilter = $arParams["FILTER"]->ObtainFullBitrixFilter();
        if ($Property->GetData('IBLOCK_ID') != $arParams['MAIN_IBLOCK']) {
            if (!empty($BitrixFilter)) {
                $arFilter = array_merge($arFilter, $BitrixFilter);
            }

            if ($Property->IsOffer()) {
                $rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID"));
                while ($arItem = $rsItems->Fetch()) {
                    $arItemsIDs[] = $arItem["ID"];
                }
                $arFilter = array(
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => $Property->GetData("IBLOCK_ID"),
                    "PROPERTY_" . $arParams["OFFERS_PROPERTY_ID"] => $arItemsIDs,
                    "!" . $property => false,
                    array(
                        "LOGIC" => "OR",
                        array(">PROPERTY_QUANTITY" => 0),
                        array("=PROPERTY_QUANTITY" => 0, "!PROPERTY_CAN_BE_ORDERED" => false),
                    )
                );
                $BitrixSubFilter = $SelectedValues->CreateBitrixFilter();
                if (!empty($BitrixSubFilter["OFFER=>MAIN"])) {
                    $arFilter = array_merge($arFilter, $BitrixSubFilter["OFFER=>MAIN"]);
                }
            } else {
                //TODO
            }
        } else {
            $arFilter["!" . $property] = false;

            $arFilter[] = array(
                "LOGIC" => "OR",
                array(">TOTAL_QUANTITY" => 0),
                array("=TOTAL_QUANTITY" => 0, "!PROPERTY_CAN_BE_ORDERED" => false),
            );

            if (!empty($arParams["SECTION_ID"])) {
                $arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
                $arFilter["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"];
            }

            // Добавил возможность указывать в свойствах фильтра дополнитеные свойства
            if (!empty($arParams["MORE_PROPERTY"])) {
                foreach ($arParams["MORE_PROPERTY"] as $key => $value) {
                    $arFilter[$key] = $value;
                }
            }
        }

        $arFilterFull = $arFilter;


        // Отключаем фильтры, оставляем только фильтр по умолчанию
        /*if(!empty($BitrixFilter)){
            $arFilter = array_merge($arFilter, $BitrixFilter);
        }*/

        // вычисляем min/max для текущих значений фильтра
        $i = $minPrice = $maxPrice = 0;
        $minPriceElement = \CIBlockElement::GetList(
            array($property => 'ASC'), $arFilter, array($property), array('nTopCount' => 1)
        )->Fetch();
        if ($minPriceElement) {
            $minPrice = $minPriceElement[$property . '_VALUE'];
        }
        $maxPriceElement = \CIBlockElement::GetList(
            array($property => 'DESC'), $arFilter, array($property), array('nTopCount' => 1)
        )->Fetch();
        $maxPrice = $maxPriceElement ? $maxPriceElement[$property . '_VALUE'] : $minPrice;

        if ($minPrice != $maxPrice) {
            $Property->SetValue('min', array('NAME' => $minPrice, 'VALUE' => $minPrice), false);
            $Property->SetValue('max', array('NAME' => $maxPrice, 'VALUE' => $maxPrice), false);
        }


        if ($SelectedValues->IsSelected($Property->GetID())) {
            $tmp = $SelectedValues->Get($Property->GetID());
            foreach ($tmp["VALUES"] as $valueID => $valueName) {
                $SelectedValues->SetValueName($Property->GetID(), $valueID, $valueID);
            }
        }

        break;
    case "S":
        $property = "PROPERTY_" . $Property->GetID();
        $arFilter = array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $Property->GetData("IBLOCK_ID"),
            "!" . $property => false
        );

        $HLValues = [];
        
        if ($Property->getUserType() == 'directory') {
            $HLValues = app('CatalogService')->getDictionaryItemsByPropertyData($Property->GetData());
        }
        
        //new \dBug($HLValues);

        $i = 0;
        $rsElements = CIBlockElement::GetList(array(), $arFilter, array($property), false, array($property));
        $elementsCount = $rsElements->SelectedRowsCount();
        while ($arElement = $rsElements->Fetch()) {
            //new \dBug($arElement);
            $i++;
            $arProperty = array(
                "ID" => $arElement[$property . "_VALUE"],
                "NAME" => $arElement[$property . "_VALUE"],
                "COUNT" => 0,
                "~COUNT" => 0
            );

            if ($Property->getUserType() == 'directory') {
                $hlValue = $HLValues[$arElement[$property . "_VALUE"]];
                if(!empty($hlValue)){
                    $arProperty['NAME'] = $Property->getHLName($hlValue, $arParams["LANG"]);
                    if (empty($arProperty['NAME'])) {
                        continue;
                    }
                    $arProperty['CODE'] = $hlValue['UF_CODE'];
                    if(!empty($hlValue['UF_FILE']))
                    {
                        $arProperty['FILE'] = CFile::GetPath($hlValue['UF_FILE']);
                    }
                }
            }
            $needToSort = ($i == $elementsCount);
            $Property->SetValue($arProperty["ID"], $arProperty, $needToSort);

            if ($SelectedValues->IsSelected($Property->GetID(), $arProperty["ID"])) {
                $SelectedValues->SetValueName($Property->GetID(), $arProperty["ID"], $arProperty["NAME"]);
            }
        }
        //new \dBug($arProperty);
        break;
    case "L":
        $arFilter = array(
            "IBLOCK_ID" => $Property->GetData("IBLOCK_ID"),
        );
        $arSelect = array(
            "ID", "IBLOCK_ID", "NAME", "XML_ID", "SORT"
        );

        $rsProperty = CIBlockProperty::GetPropertyEnum($Property->GetID(), array("SORT" => "ASC", "VALUE" => "ASC"), $arFilter, false, false, $arSelect);
        while ($arProperty = $rsProperty->GetNext()) {
            $arProperty["NAME"] = $arProperty["VALUE"];
            $arProperty["~NAME"] = $arProperty["~VALUE"];

            $Property->SetValue($arProperty["ID"], $arProperty);
            if ($SelectedValues->IsSelected($Property->GetID(), $arProperty["ID"])) {
                $SelectedValues->SetValueName($Property->GetID(), $arProperty["ID"], $arProperty["NAME"]);
            }
        }

        break;
    case "E": //привязка к элементам инфоблока
        if ($Property->GetData("LINK_IBLOCK_ID") > 0 || $Property->GetParam("LINKIBLOCKID") > 0) {

            // пока прикручиваю обработку свойства каталога
            if ($Property->IsOffer()) {
                $arFilter = array(
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => $arParams["MAIN_IBLOCK"],
                );

                if (!empty($arParams["SECTION_ID"])) {
                    $arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
                    $arFilter["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"];
                }

                // Добавил возможность указывать в свойствах фильтра дополнитеные свойства
                if (!empty($arParams["MORE_PROPERTY"])) {
                    foreach ($arParams["MORE_PROPERTY"] as $key => $value) {
                        $arFilter[$key] = $value;
                    }
                }

                //Выбираем товары с учетом уже выбранных ранее фильтров
                $arItemsIDs = array();


                $rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID"));
                while ($arItem = $rsItems->Fetch()) {
                    $arItemsIDs[] = $arItem["ID"];
                }

                if (!empty($arItemsIDs)) {
                    //Для выбранных товаров выбираем их торговые предложения и группируем их по текущему свойству и товару, к которому они привязаны
                    $arSubFilter = array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => $Property->GetData("IBLOCK_ID"),
                        "PROPERTY_" . $arParams["OFFERS_PROPERTY_ID"] => $arItemsIDs,
                        "!" . $property => false,
                        array(
                            "LOGIC" => "OR",
                            array(">PROPERTY_QUANTITY" => 0),
                            array("=PROPERTY_QUANTITY" => 0, "!PROPERTY_CAN_BE_ORDERED" => false),
                        )
                    );
                }

                $arFilter = array(
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => (($Property->GetData("LINK_IBLOCK_ID") > 0) ? $Property->GetData("LINK_IBLOCK_ID") : $Property->GetParam("LINKIBLOCKID")),
                    "ID" => CIBlockElement::SubQuery($property, $arSubFilter)
                );

            } else {
                $arSubFilter[] = array(
                    "LOGIC" => "OR",
                    array(">PROPERTY_TOTAL_QUANTITY" => 0),
                    array("=PROPERTY_TOTAL_QUANTITY" => 0, "!PROPERTY_CAN_BE_ORDERED" => false),
                );

                if (!empty($arParams["SECTION_ID"])) {
                    $arSubFilter["SECTION_ID"] = $arParams["SECTION_ID"];
                    $arSubFilter["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"];
                }

                $arFilter = array(
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => (($Property->GetData("LINK_IBLOCK_ID") > 0) ? $Property->GetData("LINK_IBLOCK_ID") : $Property->GetParam("LINKIBLOCKID")),
                    "ID" => CIBlockElement::SubQuery($property, array_merge(array(
                        "ACTIVE" => 'Y',
                        "IBLOCK_ID" => $Property->GetData('IBLOCK_ID'),
                        "!" . $property => false,
                    ), $arSubFilter))
                );
            }

            $arSelect = array(
                "ACTIVE", "ID", "IBLOCK_ID", "SORT", "NAME", "XML_ID", "IBLOCK_SECTION_ID", "CODE", "PREVIEW_PICTURE"
            );

            $rsElements = CIBlockElement::GetList(array("SORT" => "ASC", "NAME" => "ASC"), $arFilter, false, false, $arSelect);

            while ($arElement = $rsElements->Fetch()) {
                $arElement["COUNT"] = 0;
                $arElement["~COUNT"] = 0;

                $Property->SetValue($arElement["ID"], $arElement);
                if ($SelectedValues->IsSelected($Property->GetID(), $arElement["ID"])) {
                    $SelectedValues->SetValueName($Property->GetID(), $arElement["ID"], $arElement["NAME"]);
                }
            }
        }
        break;
    case "G": //привязка к секциям
        if ($Property->GetData("LINK_IBLOCK_ID") > 0) {
            $arFilter = array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $Property->GetData("LINK_IBLOCK_ID")
            );

            $rsSection = CIBlockSection::GetList(array("SORT" => "ASC", "NAME" => "ASC"), $arFilter);
            while ($arSection = $rsSection->GetNext()) {
                $arSection["COUNT"] = 0;

                $Property->SetValue($arSection["ID"], $arSection);
                if ($SelectedValues->IsSelected($Property->GetID(), $arSection["ID"])) {
                    $SelectedValues->SetValueName($Property->GetID(), $arSection["ID"], $arSection["NAME"]);
                }
            }
        }
        break;
}

if (!function_exists("GetRightValue")) {
    function GetRightValue($arElement, $PropertyID)
    {
        return isset($arElement["PROPERTY_" . $PropertyID . "_ENUM_ID"]) ? $arElement["PROPERTY_" . $PropertyID . "_ENUM_ID"] : $arElement["PROPERTY_" . $PropertyID . "_VALUE"];
    }
}

//Если нужно ищем количество элементов для каждого свойства

if ($Property->GetParam("SHOWCOUNT") === "Y" && $Property->ValuesCount() > 0) {
    $PropertyID = $Property->GetID();
    $arFilter = array(
        "ACTIVE" => "Y",
        "IBLOCK_ID" => $arParams["MAIN_IBLOCK"],
    );

    if (!empty($arParams["SECTION_ID"])) {
        $arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
        $arFilter["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"];
    }

    // Добавил возможность указывать в свойствах фильтра дополнитеные свойства
    if(is_array($arParams['MORE_PROPERTY'])){
        $arFilter = array_merge($arFilter, $arParams['MORE_PROPERTY']);
    }

    $BitrixFilter = $arParams["FILTER"]->ObtainFullBitrixFilter($PropertyID, true);

    $Prefix = "";
    $NonePrefix = '';

    if ($SelectedValues->IsSelected($PropertyID)) {
        $Prefix = "+";
    }

    //Если свойстов не принадлежит инфоблоку для которого фильтруется товары, тогда возможны 2 варианта:
    if ($Property->GetData("IBLOCK_ID") != $arParams["MAIN_IBLOCK"]) {
        //1. Фильтруемый инфоблок - товары, а свойство из торговых предложений
        if ($Property->IsOffer()) {
            //Выбираем товары с учетом уже выбранных ранее фильтров
            $arItemsIDs = array();

            $rsItems = CIBlockElement::GetList(array(), array_merge($arFilter, $BitrixFilter), false, false, array("ID"));
            while ($arItem = $rsItems->Fetch()) {
                $arItemsIDs[] = $arItem["ID"];
            }

            if (!empty($arItemsIDs)) {
                //Для выбранных товаров выбираем их торговые предложения и группируем их по текущему свойству и товару, к которому они привязаны
                $arFilter = array(
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => $Property->GetData("IBLOCK_ID"),
                    "PROPERTY_" . $arParams["OFFERS_PROPERTY_ID"] => $arItemsIDs,
                    "!PROPERTY_" . $PropertyID => false
                );

                //$BitrixSubFilter =  $SelectedValues->CreateBitrixFilter($PropertyID, false);
                $BitrixSubFilter = $SelectedValues->CreateBitrixFilter($PropertyID, true);

                if (!empty($BitrixSubFilter["OFFER=>MAIN"])) {
                    $arFilter = array_merge($arFilter, $BitrixSubFilter["OFFER=>MAIN"]);
                }

                $arGroup = array(
                    "PROPERTY_" . $PropertyID,
                    "PROPERTY_" . $arParams["OFFERS_PROPERTY_ID"]
                );

                $arCounts = $arOffers = array();
                $arFilteredItemsIDs = array();

                $rsOffers = CIBlockElement::GetList(array(), $arFilter, $arGroup);
                while ($arOffer = $rsOffers->Fetch()) {
                    $PropertyValue = GetRightValue($arOffer, $PropertyID);
                    $ItemID = GetRightValue($arOffer, $arParams["OFFERS_PROPERTY_ID"]);

                    if ($SelectedValues->IsSelected($PropertyID, $PropertyValue)) {
                        //если свойство выбрано, то
                        //исключаем этот товар(коллекцию), чтобы на количество не влияли другие торговые предложения этого товара
                        $arFilteredItemsIDs[$ItemID][] = $PropertyValue;
                    }
                    $arOffers[$ItemID][] = $PropertyValue;
                }
                //а дальше - 3 цикла. пара-пара-пам!
                if (!empty($arOffers)) {
                    //посчитаем количество товаров(исключая ненужные) для каждого значения свойства
                    //$arFilteredItemsIDs = array_unique($arFilteredItemsIDs);
                    foreach ($arOffers as $ItemID => $PropertyValues) {
                        foreach ($PropertyValues as $PropertyValue) {
                            if (!(isset($arFilteredItemsIDs[$ItemID]) && in_array($PropertyValue, $arFilteredItemsIDs[$ItemID]))) {
                                if (isset($arFilteredItemsIDs[$ItemID])) {
                                    //$arCounts[$PropertyValue]["CNT"] = -1;
                                    $arCounts[$PropertyValue]["PREFIX"] = $Prefix;
                                    $arCounts[$PropertyValue]["INTERSECTION"] = "Y";
                                } else {
                                    $arCounts[$PropertyValue]["CNT"]++;
                                    $arCounts[$PropertyValue]["SELECTED"] = "N";
                                    $arCounts[$PropertyValue]["PREFIX"] = $Prefix;
                                }
                            } else {
                                $arCounts[$PropertyValue]["SELECTED"] = "Y";
                                $arCounts[$PropertyValue]["PREFIX"] = $NonePrefix;
                                $arCounts[$PropertyValue]["CNT"] = -1;
                            }
                        }
                    }

                    // Обрабатываем те свойства, которые уже встречаются в товарах, выбранных в фильтре
                    foreach ($arCounts as $key => $value)
                        if ($value["INTERSECTION"] == "Y" && empty($value["CNT"])) $arCounts[$key]["CNT"] = -1;

                    //присвоим каждому значению нужного свойства к-во коллекций
                    if (!empty($arCounts)) {
                        foreach ($arCounts as $PropertyValue => $ItemsCount) {
                            $Property->SetValueValue($PropertyValue, "COUNT", (int)$ItemsCount["CNT"]);
                            $Property->SetValueValue($PropertyValue, "~COUNT", $ItemsCount["PREFIX"] . $ItemsCount["CNT"]);
                        }
                    }
                }
            }
        } //2.Фильтруемый инфоблок - торговые предложения а свойство из инфоблока товаров
        else {
            $SubQueryFilter = array_merge($arFilter, $BitrixFilter);
            $arFilter = array(
                "IBLOCK_ID" => $Property->GetData['IBLOCK_ID'],
                "ACTIVE" => "Y",
                "!PROPERTY_" . $PropertyID => false,
                "ID" => CIBlockElement::SubQuery("PROPERTY_" . $arParams["OFFERS_PROPERTY_ID"], $SubQueryFilter)
            );
            $BitrixSubFilter = $SelectedValues->CreateBitrixFilter($PropertyID);
            if (!empty($BitrixSubFilter['MAIN=>OFFER'])) {
                $arFilter = array_merge($arFilter, $BitrixSubFilter['MAIN=>OFFER']);
            }
            $arModelIDs = array();
            $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, array('ID', 'IBLOCK_ID', 'PROPERTY_' . $PropertyID));
            while ($arElement = $rsElements->Fetch()) {
                $PropertyValue = GetRightValue($arElement, $PropertyID);
                $arModelIDs[] = $arElement['ID'];
                $arModelsProperty[$arElement['ID']] = $PropertyValue;
            }

            //Получаем товарные предложения для найденных моделей
            if (!empty($arModelIDs)) {
                $arFilter = array(
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => $arParams["MAIN_IBLOCK"],
                    "PROPERTY_" . $arParams["OFFERS_PROPERTY_ID"] => $arModelIDs
                );
                $BitrixSubFilter = $SelectedValues->CreateBitrixFilter($PropertyID, true);
                if (!empty($BitrixSubFilter)) {
                    if (!isset($BitrixSubFilter['MAIN=>MAIN'])) {
                        $BitrixSubFilter['MAIN=>MAIN'] = $BitrixSubFilter;
                    }
                    if (!empty($BitrixSubFilter['MAIN=>MAIN'])) {
                        $arFilter = array_merge($arFilter, $BitrixSubFilter['MAIN=>MAIN']);
                    }
                }
                $rsElements = CIBlockElement::GetList(array(), $arFilter, array("PROPERTY_" . $arParams["OFFERS_PROPERTY_ID"]));
                while ($arElement = $rsElements->Fetch()) {
                    $PropertyValue = GetRightValue($arElement, $arParams["OFFERS_PROPERTY_ID"]);
                    $arModelOffersCount[$PropertyValue] = $arElement['CNT'];
                }
                $arPropertiesCount = array();
                foreach ($arModelsProperty as $ModelID => $PropertyValue) {
                    $arPropertiesCount[$PropertyValue] += $arModelOffersCount[$ModelID];
                }

                foreach ($arPropertiesCount as $PropertyValue => $PropertyCount) {
                    $Property->SetValueValue($PropertyValue, "COUNT", (int)$PropertyCount);
                    $Property->SetValueValue($PropertyValue, "~COUNT", $Prefix . $PropertyCount);
                }
            }
        }
    } else {
        $arFilter["!PROPERTY_" . $PropertyID] = false;

        $Prefix = "";
        $NonePrefix = "";

        if ($SelectedValues->IsSelected($PropertyID)) {
            $Prefix = "+";
        }

        $rsElements = CIBlockElement::GetList(array(), array_merge($arFilter, $BitrixFilter), array("PROPERTY_" . $PropertyID));

        while ($arElement = $rsElements->Fetch()) {

            $ValueID = GetRightValue($arElement, $PropertyID);

            if (!$SelectedValues->IsSelected($PropertyID, $ValueID)) {
                $ValueData = $Property->GetValue($ValueID);
                if (isset($ValueData)) {
                    $Property->SetValueValue($ValueID, "COUNT", (int)$arElement["CNT"]);
                    $Property->SetValueValue($ValueID, "~COUNT", $Prefix . $arElement["CNT"]);
                }
            }
        }
    }
    //$Property->RemoveNotExistValues();
}


//отображение
$templates = $Property->GetParam("TEMPLATE");
if($Property->getParam('CODE') == 'SIZES')
    $templates[] = 'size';
if($Property->getParam('CODE') == 'MIN_PRICE')
	$templates[] = 'price';
if(!is_array($templates)){
    $templates = [$templates];
}
$tpl_path  = "/template.php";
$result_modifier_path = "/result_modifier.php";

//! путь к шаблону фильтра в папке /local/template/.default
$default_filter_path = $_SERVER['DOCUMENT_ROOT'] . "/local/templates/.default/components/aniart/custom.filter.oop/filters/.default/";
//! путь к шаблону фильтра в папке текущего шаблона
$current_filter_path = $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . "/components/aniart/custom.filter.oop/filters/.default/";
//! путь к папке компонента /bitrix/components/aniart/custom.filter.oop
$component_filter_path = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/components/aniart/custom.filter.oop/filters/.default/";

//! сперва проверяем наличие папки фильтра в шаблоне компонента в .default
//  затем в папке текущего шаблона сайта и только в последнюю очередь
//  в папке, где располагается сам компонент

$result = array_combine(array_flip($templates), '');
foreach($templates as $tpl_name) {
    if (file_exists($default_filter_path . $tpl_name)) {
        $filter_path = $default_filter_path;
    } elseif (file_exists($current_filter_path . $tpl_name)) {
        $filter_path = $current_filter_path;
    } else {
        $filter_path = $component_filter_path;
    }

    ob_start();
    if (file_exists($filter_path . $tpl_name)) {
        if (file_exists($filter_path . $tpl_name . $result_modifier_path))
            include $filter_path . $tpl_name . $result_modifier_path;
        
        if (file_exists($filter_path . $tpl_name . $tpl_path)) {
            include $filter_path . $tpl_name . $tpl_path;
        } else {
            ShowError("Не найден шаблон \"{$Property->GetParam("TEMPLATE")}\" для фильтра \"{$Property->GetParam("TYPE")}\" в {$filter_path}");
        }

    }
    $result[$tpl_name] = ob_get_contents();
    ob_end_clean();
}

return $result;