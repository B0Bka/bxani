<?
namespace Seo\Filter\XmlFilter;

class CustomFacet extends \Bitrix\Iblock\PropertyIndex\Facet
{
    public function removeFilterType($propertyId)
    {
        $facetId = $this->storage->propertyIdToFacetId($propertyId);
        unset($this->where[$facetId]);
    }
}

class XmlSection
{
    protected $IBLOCK_ID = 0;
    protected $SKU_IBLOCK_ID = 0;
    protected $SKU_PROPERTY_ID = 0;
    protected $SECTION_ID = 0;
    protected $sections = [];
    protected $arResult = array();
    protected $arItemsBackup = array();
    protected $arCombinations = array();
    protected $props = array();

    protected $facet = null;
    protected $IDTOXML = array();
    protected $XMLTOID = array();
    protected $langDir = '/';

    private $curFilter = array();
    private $depthIterLevel = 0;

    const maxdepth = 1;
    const filterHome = 'catalog/';
    const filterGlue = '-is-';
    const filterSign = 'filter/';

    public function __construct($sectionId, $iblockId)
    {
        if(empty($iblockId))
            die('Catalog Iblock ID - does not exist');

        $this->IBLOCK_ID = $iblockId;

        $this->SECTION_ID = $sectionId ? $sectionId : 0;
        $this->facet = new CustomFacet($this->IBLOCK_ID);
        $this->section = $this->getSection();
        $this->props = $this->getIblockProps();
    }

    public function getHandbook($tblName)
    {
        global $DB;
        if (!$this->{$tblName})
        {
            $hlStrSql = 'SELECT * FROM ' . $tblName;
            $res = $DB->Query($hlStrSql, false);
            while ($rowHl = $res->Fetch()) {
            $this->{$tblName}[$rowHl['UF_XML_ID']] = $rowHl;
            }
        }
        return $this->{$tblName};
    }

    public function getIblockProps()
    {
        if (!$this->props)
        {
            $properties = \CIBlockProperty::GetList(
                ["sort" => "asc", "name" => "asc"],
                ["ACTIVE" => "Y", "IBLOCK_ID" => $this->IBLOCK_ID]
            );
            while ($prop = $properties->Fetch())
            {
                $this->props[$prop['ID']] = $prop;
            }
        }
        return $this->props;
    }

    public function getSection()
    {
        if (!$this->section)
        {
            $resDb = \CIBlockSection::GetList(
            ['SORT' => 'ASC'],
            ['IBLOCK_ID' => $this->IBLOCK_ID, 'ID' => $this->SECTION_ID,  'GLOBAL_ACTIVE' => 'Y', 'ACTIVE' => 'Y'],
            false,
            ['ID', 'NAME', 'CODE', 'ACTIVE', 'DEPTH_LEVEL', 'SECTION_PAGE_URL', 'IBLOCK_CODE', 'SECTION_PAGE_URL']);
            while ($sec = $resDb->GetNext())
            {
                $this->section[$sec['ID']] = $sec;
            }
        }

        return $this->section;
    }

    public function getIBlockPropItems($IBLOCK_ID)
    {
        $items = array();

        foreach(\CIBlockSectionPropertyLink::GetArray($IBLOCK_ID, $this->SECTION_ID) as $PID => $arLink)
        {
            if ($arLink["SMART_FILTER"] !== "Y")
                continue;

            if ($arLink["ACTIVE"] === "N")
                continue;

            $rsProperty = \CIBlockProperty::GetByID($PID);
            $arProperty = $rsProperty->Fetch();
            if($arProperty['PROPERTY_TYPE'] == 'L' || $arProperty['PROPERTY_TYPE'] == 'E' || $arProperty['PROPERTY_TYPE'] == 'S')
            {
                $items[$arProperty["ID"]] = array(
                    "ID" => $arProperty["ID"],
                    "IBLOCK_ID" => $arProperty["IBLOCK_ID"],
                    "CODE" => $arProperty["CODE"],
                    "~NAME" => $arProperty["NAME"],
                    "NAME" => htmlspecialcharsEx($arProperty["NAME"]),
                    "PROPERTY_TYPE" => $arProperty["PROPERTY_TYPE"],
                    "USER_TYPE" => $arProperty["USER_TYPE"],
                    "USER_TYPE_SETTINGS" => $arProperty["USER_TYPE_SETTINGS"],
                    "DISPLAY_TYPE" => $arLink["DISPLAY_TYPE"],
                    "DISPLAY_EXPANDED" => $arLink["DISPLAY_EXPANDED"],
                    "FILTER_HINT" => $arLink["FILTER_HINT"],
                    "VALUES" => array(),
                );
            }
        }
        return $items;
    }

    public function getResultPropItems()
    {
        $items = $this->getIBlockPropItems($this->IBLOCK_ID);
        $this->arResult["PROPERTY_COUNT"] = count($items);
        $this->arResult["PROPERTY_ID_LIST"] = array_keys($items);

        if($this->SKU_IBLOCK_ID)
        {
            $this->arResult["SKU_PROPERTY_ID_LIST"] = array($this->SKU_PROPERTY_ID);
            foreach($this->getIBlockPropItems($this->SKU_IBLOCK_ID) as $PID => $arItem)
            {
                $items[$PID] = $arItem;
                $this->arResult["SKU_PROPERTY_COUNT"]++;
                $this->arResult["SKU_PROPERTY_ID_LIST"][] = $PID;
            }
        }

        return $items;
    }

    public function fillItemValues(&$resultItem, $arProperty, $flag = null)
    {
        static $cache = array();
        if(is_array($arProperty))
        {
            if(isset($arProperty["PRICE"]))
                return null;

            $key = $arProperty["VALUE"];
            $PROPERTY_TYPE = $arProperty["PROPERTY_TYPE"];
            $PROPERTY_USER_TYPE = $arProperty["USER_TYPE"];
            $PROPERTY_ID = $arProperty["ID"];
        }
        else
        {
            $key = $arProperty;
            $PROPERTY_TYPE = $resultItem["PROPERTY_TYPE"];
            $PROPERTY_USER_TYPE = $resultItem["USER_TYPE"];
            $PROPERTY_ID = $resultItem["ID"];
            $arProperty = $resultItem;
        }

        if($PROPERTY_TYPE == "E" && $key <= 0)
        {
            return null;
        }
        elseif(strlen($key) <= 0)
        {
            return null;
        }

        $htmlKey = htmlspecialcharsbx($key);
        if (isset($resultItem["VALUES"][$htmlKey]))
        {
            return $htmlKey;
        }

        $arUserType = array();
        $file_id = null;
        $xmlID = "";
        switch($PROPERTY_TYPE)
        {
            case "L":
                $enum = \CIBlockPropertyEnum::GetByID($key);

                if ($enum)
                {
                    $value = $enum["VALUE"];
                    $sort  = $enum["SORT"];
                    $id=$enum["ID"];
                    $xmlID = $enum['XML_ID'];
                }
                else
                {
                    return null;
                }
                break;
            case "E":
                if(!isset($cache[$PROPERTY_TYPE][$key]))
                {
                    $arLinkFilter = array (
                    "ID" => $key,
                    "ACTIVE" => "Y",
                    "ACTIVE_DATE" => "Y",
                    "CHECK_PERMISSIONS" => "Y",
                    );
                    $rsLink = \CIBlockElement::GetList(array(), $arLinkFilter, false, false, array("ID","IBLOCK_ID","NAME","SORT", "CODE"));
                    $cache[$PROPERTY_TYPE][$key] = $rsLink->Fetch();
                }

                $value = $cache[$PROPERTY_TYPE][$key]["NAME"];
                $sort = $cache[$PROPERTY_TYPE][$key]["SORT"];
                $id=$key;
                $xmlID = $cache[$PROPERTY_TYPE][$key]["CODE"];
                break;
            case "S":
                $value = $key;
                $sort = 0;
                break;
        }

        $keyCrc = abs(crc32($htmlKey));
        $value = htmlspecialcharsex($value);
        $sort = (int)$sort;

        $trsl_value = \Cutil::translit($value, LANGUAGE_ID, array(
            "max_len" => 100,
            "change_case" => 'L',
            "replace_space" => '_',
            "replace_other" => '',
            "delete_repeat_replace" => true,
        ));


        $filterPropertyID = $this->SAFE_FILTER_NAME.'_'.$PROPERTY_ID;
        $filterPropertyIDKey = $filterPropertyID.'_'.$keyCrc;
        $resultItem["VALUES"][$htmlKey] = array(
            "CONTROL_ID" => $filterPropertyIDKey,
            "CONTROL_NAME" => $filterPropertyIDKey,
            "CONTROL_NAME_ALT" => $filterPropertyID,
            "HTML_VALUE_ALT" => $keyCrc,
            "HTML_VALUE" => "Y",
            "VALUE" => $value,
            "XML_ID" => $xmlID,
            "SORT" => $sort,
            "UPPER" => ToUpper($value),
            "FLAG" => $flag,
            "TRANSLIT" => $id,
        );

        return $htmlKey;
    }

    public function genAllItems()
    {
        $this->arResult["FACET_FILTER"] = false;
        $this->arResult["COMBO"] = array();
        $this->arResult["ITEMS"] = $this->getResultPropItems();

        if ($this->SECTION_ID > 0 && $this->facet->isValid())
        {
            $this->facet->setSectionId($this->SECTION_ID);
            $this->arResult["FACET_FILTER"] = ["ACTIVE_DATE" => "Y", "CHECK_PERMISSIONS" => "Y"];
            $res = $this->facet->query( $this->arResult["FACET_FILTER"] ); //going for item vals
            while ($row = $res->fetch())
            {
                $facetId = $row['FACET_ID'];
                if (\Bitrix\Iblock\PropertyIndex\Storage::isPropertyId($facetId))
                {
                    $PID = \Bitrix\Iblock\PropertyIndex\Storage::facetIdToPropertyId($facetId);
                    if (!isset($this->arResult["ITEMS"][$PID]))
                        continue;


                    if ($this->arResult["ITEMS"][$PID]["PROPERTY_TYPE"] == "S")
                    {
                        if (!empty($row["VALUE"]))
                        {
                            $lookupDictionary = $this->facet->getDictionary()->getStringByIds([$row["VALUE"]]);
                        }
                        $addedKey = $this->fillItemValues($this->arResult["ITEMS"][$PID], $lookupDictionary[$row["VALUE"]], true);
                        if (strlen($addedKey) > 0)
                        {
                            $this->arResult["ITEMS"][$PID]["VALUES"][$addedKey]["FACET_VALUE"] = $row["VALUE"];
                            $this->arResult["ITEMS"][$PID]["VALUES"][$addedKey]["ELEMENT_COUNT"] = $row["ELEMENT_COUNT"];
                        }
                    }
                    else
                        $addedKey = $this->fillItemValues( $this->arResult["ITEMS"][$PID], $row["VALUE"], true );

                    if ( $addedKey )
                        $this->arResult["ITEMS"][$PID]["VALUES"][$addedKey]["FACET_VALUE"] = $row["VALUE"];
                }
            }
            foreach($this->arResult['ITEMS'] as &$item)
            {
                foreach($item['VALUES'] as &$ival)
                {
                    $ival['DISABLED'] = true;
                }
            }
            foreach ( $this->arResult["ITEMS"] as $key => $Property )
            {
                foreach ( $Property["VALUES"] as $k => $arValue )
                {
                    if (empty($arValue["VALUE"]))
                    {
                        unset($this->arResult["ITEMS"][$key]["VALUES"][$k]);
                    }

                    if ( strlen( $arValue["TRANSLIT"] ) > 0 )
                    {
                        $this->arResult["ALL_PROPS"][$arValue["TRANSLIT"]] = $Property["CODE"];
                        $this->arResult["ALL_PROPS_VALS"][$arValue["TRANSLIT"]] = $arValue["VALUE"];
                    }
                }
            }
            $this->arItemsBackup = $this->arResult['ITEMS'];
        }
        else
            die('facet index is not valid, launch when this will be fixed');
    }

    public function  addCombination($combo)
    {
        //check validity
        $this->arCombinations[$this->depthIterLevel][] = serialize($combo);
        //check amount, if too large then save it to database
    }

    //probably recursive
    public function genCombinations($ITEMS = array())
    {
        $this->depthIterLevel++;
        $facetIndex = array();
        if(empty($ITEMS))
            $ITEMS = $this->arItemsBackup;

        reset($ITEMS);
        $FORWARD_ITEMS = $ITEMS;
        foreach($ITEMS as $PID => &$item)
        {
            foreach($item['VALUES'] as $key => &$val)
            {
            $facetIndex[$PID][$val["FACET_VALUE"]] = &$val;
            }
        }
        if(isset($item))
            unset($item);
        if(isset($val))
            unset($val);

        $res = $this->facet->query( array() );

        while ( $row = $res->fetch() )
        {
            $facetId = $row["FACET_ID"];
            if ( \Bitrix\Iblock\PropertyIndex\Storage::isPropertyId( $facetId ) )
            {
                $pp = \Bitrix\Iblock\PropertyIndex\Storage::facetIdToPropertyId( $facetId );

                if ( isset( $facetIndex[$pp][$row["VALUE"]] ) )
                {
                    unset( $facetIndex[$pp][$row["VALUE"]]["DISABLED"] );
                }
            }
        }

        foreach($ITEMS as $PID => &$item)
        {
            if(isset($this->curFilter[$PID]))
                continue;
            if(in_array($PID, $this->skipParents))
                continue;
            foreach($item['VALUES'] as $key => &$val)
            {
                if(isset($val['DISABLED']))
                    continue;
                if(empty($val))
                    continue;

                $this->curFilter[$PID] = ['VALUE' => $val['VALUE'], 'TYPE' => $item['PROPERTY_TYPE'], 'TRANSLIT' => $val['TRANSLIT']];
                $this->facet->addDictionaryPropertyFilter( $PID, "=", $val['TRANSLIT'] );
                $this->addCombination($this->curFilter);
                $this->facet->removeFilterType($PID);
                unset($this->curFilter[$PID]);

            }
            if($this->depthIterLevel == 1)
                unset($this->arItemsBackup[$PID]);//all possible combination of this type is found (only if it at iter base)

            unset($FORWARD_ITEMS[$PID]);
        }
        $this->depthIterLevel--;
        return true;
    }

    public function getUrlFromCombo($combo)
    {
        if(!is_array($combo) || count($combo) < 1)
            return false;
        $sectionPath = $this->langDir;
        if ($this->SECTION_ID && array_key_exists($this->SECTION_ID, $this->section))
        {
            $sectionPath = $this->section[$this->SECTION_ID]['SECTION_PAGE_URL'];
        }
        $url = $sectionPath . self::filterSign;

        foreach($combo as $PID => $val)
        {
            $propCode = '';
            if (array_key_exists($PID, $this->arResult['ITEMS']))
                $propCode = mb_strtolower($this->arResult['ITEMS'][$PID]['CODE']);

            $propVal = strtolower($this->idToXMLID($val['VALUE']));
            $url .= $propCode . self::filterGlue . $propVal . '/';
            if ($PID == 340 && intVal($propVal))
                $url = false;
        }

        return $url ? $url . 'apply/' : false;
    }

    private function generateIdsArrays(){
        //find array of ['id'] = "XML_ID"
        foreach($this->arResult['ITEMS'] as $key => &$value)
        {
            foreach($value['VALUES'] as $k => &$v)
            {
                if(isset($v['XML_ID']) && strlen($v['XML_ID']) > 0)
                {
                    $v['XML_ID'] = strtolower($v['XML_ID']);
                    if(!isset($this->XMLTOID[$v['XML_ID']]))
                        $this->XMLTOID[$v['XML_ID']] = $k;
                    $this->IDTOXML[$k]['value'] = $v['XML_ID'];
                    $this->IDTOXML[$k]['parent'] = $key;
                }
            }
            unset($v);
        }
        unset($value);
    }

    public function idToXMLID($id)
    {
        if(empty($this->IDTOXML))
        {
            $this->generateIdsArrays();
        }

        if(is_array($id))
        {
            $parents = array();
            foreach($id as $value)
            {
                if(isset($this->IDTOXML[$value]['parent']) && intval($this->IDTOXML[$value]['parent']) > 0)
                {
                    if(isset($parents[(int)$this->IDTOXML[$value]['parent']]))
                        $parents[(int)$this->IDTOXML[$value]['parent']]++;
                    else
                        $parents[(int)$this->IDTOXML[$value]['parent']] = 1;
                }
            }

            $xmlids = array();
            foreach($id as $key => $val)
            {
                if(isset($this->IDTOXML[$val]['value']) && strlen($this->IDTOXML[$val]['value']) > 0  && $parents[$this->IDTOXML[$val]['parent']] == 1)
                    $xmlids[$key] = $this->IDTOXML[$val]['value'];
                elseif(isset($this->IDTOXML[$val]['value']))
                    $xmlids[$key] = $val;
            }

            return $xmlids;
        }
        else
        {
            if(isset($this->IDTOXML[$id]['value']) && strlen($this->IDTOXML[$id]['value']) > 0)
                return $this->IDTOXML[$id]['value'];
            else
                return $id;
        }
    }

    protected function getCountElements($filterVals)
    {
        if(empty($filterVals) || !is_array($filterVals))
            die('empty or not array');

        $arFilter = array(
            'IBLOCK_ID' => $this->IBLOCK_ID,
            'SECTION_ID' => $this->SECTION_ID,
            "INCLUDE_SUBSECTIONS" => "Y",
            'ACTIVE' => 'Y'
        );
        foreach($filterVals as $PID => $val)
        {
            if(intval($PID) <= 0 || empty($val))
                die('skipping' . var_export($filterVals));
            if($val['TYPE'] == 'E')
                $arFilter['PROPERTY_'.$PID] = $val['TRANSLIT'];
            else
                $arFilter['PROPERTY_'.$PID.'_VALUE'] = $val['VALUE'];
        }

        $obElement = \CIBlockElement::GetList([], $arFilter, array('IBLOCK_ID'));
        if($res = $obElement->Fetch())
        {
            $elemCount = intval($res['CNT']);
            if($elemCount > 0)
                return $elemCount;
        }
        die('returning zero');
    }

    public function getFilterItems()
    {
        if(empty($this->arCombinations))
        {
            $this->genAllItems();
            $this->genCombinations();
        }

        $filterItems = array();
        for($i=1; $i<=self::maxdepth; $i++)
        {
            foreach($this->arCombinations[$i] as $combo)
            {
                $combo = unserialize($combo);
                $countElements = $this->getCountElements($combo);
                if($countElements >= 1)
                {
                    $url = $this->getUrlFromCombo($combo);
                    $filterItems[] = array('LEVEL'=> $i, 'FULL_PATH' => $url);
                }
            }
        }

        return $filterItems;
    }

    protected function getSitemapItems()
    {
        $arResult = array();
        $sl = \CLang::GetList($by="sort", $order="desc");
        while ($slr = $sl->Fetch())
        {
            if ($slr['LID'] == LANG)
            {
                $this->langDir = $slr['DIR'];
                break;
            }
        }

        $filterItems = $this->getFilterItems();
        $arResult = array(
            'SECTION_ID' => $this->SECTION_ID,
            'SECTION_PAGE_URL' => $this->section[$this->SECTION_ID]['SECTION_PAGE_URL'],
            'NAME' => $this->section[$this->SECTION_ID]['NAME'],
            'ITEMS' => $filterItems
        );
        return $arResult;
    }

    public function genSectionFilterSitemap()
    {
        $arResult = $this->getSitemapItems();
        return $arResult;
    }
}

