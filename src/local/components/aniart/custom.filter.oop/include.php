<?
use Aniart\Main\CustomFilter\Models\FilterPropMetaData;
use Bitrix\Highloadblock\HighloadBlockTable;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class CustomFilterSelectedValues
{
    private $arData = array();
    private $filter;
    private $sFilterName = "";
    private $arParams = array();

    public function __construct(CustomFilter $filter, $arParams = array()){
        $this->filter = $filter;
        $this->sFilterName = $filter->GetName();
        if(is_array($arParams)) {
            $this->arParams = $arParams;
        }
    }

    public function Add($PropertyID,$PropertyParams = array(),$ValueID, $ValueName = "")
    {
        if(is_array($ValueID))
        {
            foreach($ValueID as $key=>$value)
            {
                if($PropertyID == 'PRICE'){
                    $ValueName = $value;
                    $value = $key;
                }
                if(is_array($ValueName))
                    $this->arData[$PropertyID]["VALUES"][(string)$value] = array("NAME" => $ValueName[$key], "INDEX" => $key);
                else
                    $this->arData[$PropertyID]["VALUES"][(string)$value] = array("NAME" => $ValueName, "INDEX" => $key);
            }
        }
        elseif(!empty($ValueID)){
            $this->arData[$PropertyID]["VALUES"][(string)$ValueID] = array("NAME" => $ValueName);
        }
        if(!empty($PropertyParams)){
            foreach($PropertyParams as $key=>$value){
                $this->arData[$PropertyID][$key] = $value;
            }
        }
    }

    public function RemoveByPropertyID($PropertyID){
        unset($this->arData[$PropertyID]);
    }

    public function IsSelected($PropertyID, $ValueID = ""){
        $ValueID = (string)$ValueID;
        if($ValueID){
            return isset($this->arData[$PropertyID]["VALUES"][(string)$ValueID]);
        }
        else
            return isset($this->arData[$PropertyID]);
    }

    public function GetFromRequest($PropertyID){
        return $_REQUEST[$this->sFilterName][$PropertyID];
    }

    public function SetPropertyName($PropertyID, $PropertyName){
        if($this->GetFromRequest($PropertyID)){
            $this->arData[$PropertyID]["NAME"] = $PropertyName;
        }
    }

    public function SetPropertyParam($PropertyID, $key, $value){
        if($this->isSelected($PropertyID))
            $this->arData[$PropertyID][$key] = $value;
    }

    public function GetPropertyParam($PropertyID, $key){
        if($this->isSelected($PropertyID))
            return $this->arData[$PropertyID][$key];
    }

	public function GetSelectedFiltersNames($PropertyID, $key){
		if($this->isSelected($PropertyID))
			return $this->arData[$PropertyID]["NAME"] . " " . $this->arData[$PropertyID]["VALUES"][$key]["NAME"];
	}

	public function GetFilterArData($PropertyID){
		return $this->arData[$PropertyID];
	}

    public function SetValueName($PropertyID, $ValueID, $ValueName){
        $ValueID = (string)$ValueID;
        $this->arData[$PropertyID]["VALUES"][$ValueID]["NAME"] = $ValueName;
    }

    public function SetValueParam($PropertyID, $ValueID, $key, $value)
    {
        $ValueID = (string)$ValueID;
        if($this->isSelected($PropertyID, $ValueID))
            $this->arData[$PropertyID]["VALUES"][$ValueID][$key] = $value;
    }

    public function GetValueParam($PropertyID, $ValueID, $key)
    {
        $ValueID = (string)$ValueID;
        if($this->isSelected($PropertyID, $ValueID))
            return $this->arData[$PropertyID]["VALUES"][$ValueID][$key];
    }

    public function BanHiddenFieldFor($PropertyID, $ValueID = false){
        if($ValueID || $ValueID === "0" || $ValueID === 0 ){
            $this->SetValueParam($PropertyID, (string)$ValueID, "DONT_HIDDEN_FIELD", true);
        }
        else
        {
            if($this->isSelected($PropertyID)){
                foreach($this->arData[$PropertyID]["VALUES"] as $ValueID => $ValueData){
                    $this->BanHiddenFieldFor($PropertyID, $ValueID);
                }
            }
        }
    }

    public function DontUseInSqlRequest($PropertyID){
        $this->arData[$PropertyID]["DONT_SQL"] = true;
    }

    public function NeedHiddenFieldFor($PropertyID, $ValueID){
        return $this->GetValueParam($PropertyID, $ValueID, "DONT_HIDDEN_FIELD") !== true;
    }

    public function Get($PropertyID = 0, $ValueID = 0, $ValueDataField = false)
    {
        $ValueID = (string)$ValueID;
        if($PropertyID)
        {
            if($ValueID){
                if($ValueDataField)
                    return $this->arData[$PropertyID]["VALUES"][$ValueID][$ValueDataField];
                return $this->arData[$PropertyID]["VALUES"][$ValueID];
            }
            return $this->arData[$PropertyID];
        }

        return $this->arData;
    }

    public function SelectedValuesCount(){
        return count($this->arData);
    }

    public function SelectedAllValuesCount(){
        $count = 0;
        foreach($this->arData as $propCode => $propData){
            $count+=count($propData['VALUES']);
        }
        return $count;
    }

    public function CreateBitrixFilter($CurrentPropertyID = array(), $ExcludePropertyFromFilter = false)
    {
        $arFilter = array("MAIN=>MAIN"=>array());

        if(!is_array($CurrentPropertyID)) {
            $CurrentPropertyID = array($CurrentPropertyID);
        }

        if(!empty($this->arData))
        {
            foreach($this->arData as $PropertyID => $PropertyData)
            {
                /**
                 * @var CustomFilterProperty $filterProperty
                 */
                $filterProperty = $this->filter->GetProperty($PropertyID);
                $ExcludeProperty = in_array($PropertyID, $CurrentPropertyID) && $ExcludePropertyFromFilter;

                if(!empty($PropertyData["VALUES"]) && $PropertyData["DONT_SQL"]!==true && $ExcludeProperty !== true)
                {
                    $prefix = "PROPERTY_";
                    $logic	= "OR";
                    if(in_array($PropertyID, $CurrentPropertyID)){
                        $prefix	=  "!PROPERTY_";
                        $logic	= "AND";
                    }

                    $FilterType = "MAIN=>MAIN";
                    if($PropertyData["IS_DIFFERENT"]){
                        $FilterType = $PropertyData["IS_OFFER"] ? "OFFER=>MAIN" : "MAIN=>OFFER";
                    }
                    //OTHER_PROPERTIES
                    if(array_key_exists($PropertyID, $this->arParams["OTHER_PROPERTIES"])){

                        $arrFilter = array();
                        foreach ($PropertyData["VALUES"] as $ValueID=>$ValueData)
                        {
                            $arrFilter[$PropertyID][] = $ValueID;
                        }
                        $arFilter[$FilterType]["OTHER_PROPERTIES"] = $arrFilter;

                        $arrFilter = array();
                        $BitrixFilterAddArray = array();
                        if($arFilter[$FilterType]['OTHER_PROPERTIES']){
                            foreach($arFilter[$FilterType]['OTHER_PROPERTIES'] as $otherPropID => $otherPropValue){
                                if(array_key_exists($otherPropID, $this->arParams["OTHER_PROPERTIES"])){
                                    foreach($otherPropValue as $Oval){
                                        $BitrixFilterAddArray = array_merge($BitrixFilterAddArray, $this->arParams["OTHER_PROPERTIES"][$otherPropID]["VALUES"][$Oval]["FILTER"]["ID"]);
                                    }
                                }
                            }
                        }

                        if(count($BitrixFilterAddArray)>0){
                            $arrFilter["LOGIC"] = "AND";
                            $arrFilter['ID'] = $BitrixFilterAddArray;
                        }
                        $arFilter[$FilterType][] = $arrFilter;
                    }
                    //Meta Data conditions
                    elseif($filterProperty && $filterPropMetaData = $filterProperty->getMetaData()){
                        $metaDataTemplate = $filterPropMetaData->getTemplate()->getCode();
                        if($metaDataTemplate == 'intervals'){
                            $intervals = app('FilterPropsMetaDataService')->getIntervalsValuesByCodes(array_keys($PropertyData['VALUES']));
                            $propertyValuesId = app('FilterPropsMetaDataService')
                                ->getDictionaryValuesIdUsingIntervals(
                                    $intervals, $PropertyID, (int)$this->filter->GetParam('SECTION_ID')
                                );
                            $arFilter[$FilterType][$prefix.$PropertyID] = empty($propertyValuesId) ? -1 : $propertyValuesId;
                        }
                    }
                    //END OTHER_PROPERTIES
                    elseif(count($PropertyData["VALUES"]) >= 1)
                    {
                        $priceId   = BASE_ID_PRICE;
                        $arrFilter = array("LOGIC" => $logic);
                        foreach ($PropertyData["VALUES"] as $ValueID=>$ValueData)
                        {
                            if($ValueData["INDEX"] === "f"){
                                $arrFilter["LOGIC"] = "AND";
                                if($PropertyID == 'PRICE'){
                                    $prefix = ">=CATALOG_PRICE_".$priceId;
                                }
                                else{
                                    $prefix = ">=PROPERTY_".$priceId;
                                }
                            }
                            elseif($ValueData["INDEX"] === "t"){
                                $arrFilter["LOGIC"] = "AND";
                                if($PropertyID == 'PRICE'){
                                    $prefix = "<=CATALOG_PRICE_".$priceId;
                                }
                                else{
                                    $prefix = "<=PROPERTY_".$PropertyID;
                                }
                            }
                            if($ValueData["INDEX"] === "f" || $ValueData["INDEX"] === "t")
                            {
                                if($PropertyID == 'PRICE'){
                                    $arrFilter[] = array($prefix => $ValueData['NAME']);
                                }
                                else{
                                    $arrFilter[] = array($prefix => $ValueID);
                                }
                            }
                            else {
                                $arrFilter[] = array($prefix . $PropertyID => $ValueID);
                            }
                        }
                        $arFilter[$FilterType][] = $arrFilter;
                    }
                    else $arFilter[$FilterType][$prefix.$PropertyID] = key($PropertyData["VALUES"]);
                }
            }
        }

        if(empty($arFilter["OFFER=>MAIN"]) && empty($arFilter["MAIN=>OFFER"]))
            return $arFilter["MAIN=>MAIN"];
        else
            return $arFilter;
    }
}

class CustomFilterProperty
{
    private $ID = 0;
    private $arData = array();
    private $arParams = array();
    private $bIsOffer = false;
    private $bIsVirtual = false;
    private $bIsHidden = false;
    private $sHTML = "";
    protected $filter;
	/**
	 * @var FilterPropMetaData
	 */
	protected $metaData;

    private static $arProperties = array();

    public function __construct($propertyID, $type = false/*offer or virtual*/)
    {
        $this->ID = $propertyID;

        if($type == "offer") $this->bIsOffer = true;
        elseif($type == "virtual") $this->bIsVirtual = true;

        if(isset(self::$arProperties[$propertyID]))
            $this->arData = self::$arProperties[$propertyID];
        else
            self::$arProperties[$propertyID] = array();
    }

    public function setFilter(CustomFilter $filter)
    {
        $this->filter = $filter;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getUserType()
    {
        return $this->arData["USER_TYPE"];
    }

    public function getHLTableName()
    {
        return $this->arData["USER_TYPE_SETTINGS"]["TABLE_NAME"];
    }

    public function getHLValues($HLTable)
    {
        $hlValues = app('CatalogService')->getDictionaryItems($HLTable);

        return $hlValues ? $hlValues : [];
    }

    public function getHLName($hlValue, $lang) {
        $name = $hlValue['UF_NAME'] ?: $hlValue['UF_NAME_'.strtoupper($lang)];
        return $name;
    }

	public function setMetaData(FilterPropMetaData $metaData)
	{
		$this->metaData = $metaData;
		return $this;
	}

	public function getMetaData()
	{
		return $this->metaData;
	}

    public function IsOffer() {
        return $this->bIsOffer;
    }

    public function IsVirtual() {
        return $this->bIsVirtual;
    }

    public function IsHidden() {
        return $this->bIsHidden;
    }

    public function Show(){
        $this->bIsHidden = false;
    }

    public function Hide(){
        $this->bIsHidden = true;
    }

    public function IsEmpty() {
        return empty($this->arData);
    }

    public function IsValueSelected($value) {
        if(is_array($this->arData['SELECTED_VALUES'])){
            return in_array($value, $this->arData["SELECTED_VALUES"]);
        }
        else{
            return false;
        }
    }

    public function getPropertyOpen(){
    	$filterName = 'one_filter_' . $this->ID;
	    return $this->filter->getMorePropertiesOpen($filterName);
    }

	public function getUserOpenedFilters(){
		$filtersCondition = explode(',', base64_decode($_COOKIE['filtersCondition']));
		$labelId = 'one_filter_' . self::GetID();
		return (in_array($labelId, $filtersCondition) != false) ? 'opened' : '';
	}

    public static function IsUsing($ID) {
        return isset(self::$arProperties[$ID]);
    }

    public function GetID() {return $this->ID;}

    public function GetData($key = ""){
        return	empty($key)?$this->arData:$this->arData[$key];
    }

    public function GetParams(){
        return $this->arParams;
    }

    public function GetParam($key){
        return $this->arParams[$key];
    }

    public function GetValues(){
        return $this->arData["VALUES"];
    }

    public function AddSelectedValues($arValues){
        $this->arData["SELECTED_VALUES"] = $arValues;
    }

    public function AddSelectedValue($value){
        $this->arData["SELECTED_VALUES"][] = $value;
    }

    public function GetSelectedValues(){
        return $this->arData["SELECTED_VALUES"];
    }

    public function SetValue($key, $value, $sort = true){
        $this->arData["VALUES"][$key]= $value;
        if($sort){
            uasort($this->arData['VALUES'], array($this, 'SortBySortAndName'));
        }
        return $this;
    }

    public function SortValuesByName(){
        uasort($this->arData['VALUES'], array($this, 'SortByName'));
    }

    private function SortBySort($a,$b)
    {
        if($a["SORT"] == $b["SORT"])
            return 0;
        else
            return ($a["SORT"] < $b["SORT"]) ? -1 : 1;
    }

    private function SortBySortAndName($a,$b)
    {
        if($a["SORT"] == $b["SORT"])
            return $this->SortByName($a,$b);
        else
            return ($a["SORT"] < $b["SORT"]) ? -1 : 1;
    }

    private function SortByName($a,$b)
    {
        if($a["NAME"] == $b["NAME"])
            return 0;
        else
            return ($a["NAME"] < $b["NAME"]) ? -1 : 1;
    }

    public function GetValue($key){
        return $this->arData["VALUES"][$key];
    }

    public function SetValueValue($value_id, $value_key, $value_value){
        $this->arData["VALUES"][$value_id][$value_key] = $value_value;
    }

    public function RemoveValue($value_id){
        unset($this->arData["VALUES"][$value_id]);
    }

    public function RemoveNotExistValues($exceptSelected = false){
        if(!empty($this->arData["VALUES"]))
        {
            foreach($this->arData["VALUES"] as $key => $value){
                if((int)$value["COUNT"] === 0){
                    if($exceptSelected && $this->IsValueSelected($key)){
                        continue;
                    }
                    $this->RemoveValue($key);
                }
            }
        }
    }

    public function ValuesCount($withCount = false)
    {
        $values = &$this->arData['VALUES'];
        if($withCount){
            unset($values);
            $values = array_filter($this->arData['VALUES'], function($value){
                return (/*!isset($value['COUNT']) || */$value['COUNT'] > 0 || $this->IsValueSelected($value['ID']));
            });
        }
        return count($values);
    }

    public function SetParams($arParams){
        $this->arParams = $arParams;
    }

    public function SetParam($key, $value){
        $this->arParams[$key] = $value;
    }

    public function SetData($arData)
    {
        $this->arData = $arData;
        if($this->ID > 0)
            self::$arProperties[$this->ID] = $this->arData;
    }

    public function SetDataParam($key, $value)
    {
        $this->arData[$key] = $value;
        if($this->ID > 0){
            self::$arProperties[$this->ID] = $this->arData;
        }
    }

    public function SetHtml($HTML){
        $this->sHTML = $HTML;
    }

    /*public function GetHtml($tpl = 'default')
    {
        if(is_array($this->sHTML)){
            return (string)$this->sHTML[$tpl];
        }
        return $this->sHTML;
    }*/
    
    public function GetHtml($tpl = 'main')
    {
        if(is_array($this->sHTML))
        {
            $templates = $this->arParams['TEMPLATE'];
            foreach($templates as $template)
            {
                $path = explode('_', $template);
                if(in_array($tpl, $path))
                {
                    $tpl = $template;
                    break;
                }
            }
            return (string)$this->sHTML[$tpl];
        }
        return $this->sHTML;
    }

	private function roundNearestHundredUp($number)
	{
		return ceil( $number / 100 ) * 100;
	}

	private function roundNearestHundredDown($number)
	{
		return floor ( $number / 100 ) * 100;
	}


    public function getPriceRanges($step = 5){
    	global $TRANSLATE;
	    if(isset($this->arData["VALUES"]["min"]) && isset($this->arData["VALUES"]["max"])){
	    	$rangeArray = [];
	    	$result_array = [];
		    $minPrice = $this->arData["VALUES"]["min"]["VALUE"];
		    $maxPrice = $this->arData["VALUES"]["max"]["VALUE"];
		    $range = $maxPrice - $minPrice;
		    $minFilterCheckbox = self::roundNearestHundredUp($minPrice);
		    $maxFilterCheckbox = self::roundNearestHundredUp($maxPrice);
		    $range_step = $minFilterCheckbox;
		    $step_value = self::roundNearestHundredUp($range / $step);
		    for ($i = 0; $i < $step; $i ++){
			    $rangeArray[$i] = $range_step;
			    $range_step = $range_step + $step_value;
		    }
		    $result_array[0]["min"] = $minPrice;
		    $result_array[0]["max"] = $rangeArray[0];
		    $result_array[0]["text"] = "До " . $rangeArray[0];

		    for($i = 1; $i < $step; $i ++){
			    $result_array[$i]["min"] = $rangeArray[$i-1];
			    $result_array[$i]["max"] = $rangeArray[$i];
			    $result_array[$i]["text"] = $rangeArray[$i-1] . "-" . $rangeArray[$i];
		    }

		    $result_array[$step]["min"] = $rangeArray[$step - 1];
		    $result_array[$step]["max"] = $maxPrice;
		    $result_array[$step]["text"] = "От " . $rangeArray[$step - 1];
	    }
	    return $result_array;
    }
}

class CustomFilter
{
    private $sName = "";
    private $arData = array();
    private $arParams = array();
    private $SelectedValues = false;
    private $sefController = false;
    private $openedFilters = array();

    public function __construct($filterName, $arParams = array())
    {
        if(empty($filterName))
            $this->sName = "filter";
        else $this->sName = $filterName;

        if(is_array($arParams))
            $this->arParams = $arParams;

        $this->SelectedValues = new CustomFilterSelectedValues($this, $this->arParams);
    }

    public function GetName(){
        return $this->sName;
    }

    public function GetData($key = ""){
        return empty($key)?$this->arData:$this->arData[$key];
    }

    public function PropertiesCount(){
        return count($this->arData["PROPERTIES"]);
    }

    public function GetProperties(){
        return $this->arData["PROPERTIES"];
    }

    public function GetProperty($propertyID){
        return $this->arData["PROPERTIES"][$propertyID];
    }

    public function GetSelectedValues(){
        return $this->SelectedValues;
    }

    public function GetParams(){
        return $this->arParams;
    }

    public function GetParam($key){
        return $this->arParams[$key];
    }

    public function SetParams($arParams){
        $this->arParams = $arParams;
        return $this;
    }

    public function SetParam($key, $value){
        $this->arParams[$key] = $value;
        return $this;
    }

    public function SetSEFController($sefController)
    {
        if(is_object($sefController)){
            $this->sefController = $sefController;
        }
    }

    public function GetSEFController()
    {
        return $this->sefController;
    }

    public function inSEFMode()
    {
        return is_object($this->sefController);
    }

    //Добавляет к фильтру свойства, по которым возможна фильтрация
    public function AddProperties($param, $type = false)
    {
        if(!empty($param)){
            if(is_array($param)){
                foreach($param as $v){
                    if(!empty($v))	$this->AddProperties($v, $type);
                }
            }
            elseif(!isset($this->arData["PROPERTIES"][$param])){
                $property = new CustomFilterProperty($param, $type);
                $property->setFilter($this);
                $this->arData["PROPERTIES"][$param] = $property;
            }
            else{
                $this->GetProperty($param)->Show();
            }
        }
        return $this;
    }

	public function setOpenFilters(){
    	$return = [];
    	if(isset($_COOKIE['filtersCondition'])){
    		$openFilters = explode(',', base64_decode($_COOKIE['filtersCondition']));
    		foreach ($openFilters as $key=>$filter){
			    $return[$key]["NAME"] = explode("%", $filter)[0];
			    $return[$key]["PATH"] = explode("%", $filter)[1];
		    }
	    }
		return $return;
	}

	public function getMorePropertiesOpen($filterName){
		if(app()->getDeviceType() == 'mobile'){return false;}
		$url = explode("/",$_SERVER["PHP_SELF"]);
		array_pop($url); //del "inedx.php"
		$trimmedUrl = implode('/', $url);
		$openFilters = self::setOpenFilters();
		if(isset($openFilters)){
			foreach ($openFilters as $filter){
				if($filter["NAME"] == $filterName && $filter["PATH"] == $trimmedUrl."/"){
					$res = true;
				}
			}
		}
		return $res;
	}

    public function ObtainPropertiesData()
    {
        if(!empty($this->arData["PROPERTIES"]))
        {
            $IDs = array();
            foreach($this->arData["PROPERTIES"] as $obProperty)
            {
                if($obProperty->isEmpty() && !$obProperty->isVirtual()){
                    $IDs[] = $obProperty->GetID();
                }
            }
            if(!empty($IDs))
            {
                $rsProperties = CIBlockProperty::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array()); // не фильтрует по массиву айди
                while($arProperty = $rsProperties->Fetch())
                {
                    //OTHER_PROPERTIES
                    if(array_key_exists($arProperty["ID"], $this->arParams["OTHER_PROPERTIES"])){
                        $arProperty["PROPERTY_TYPE"] = "U";
                    }
                    //end OTHER_PROPERTIES
                    if(in_array($arProperty["ID"], $IDs)){
                        $this->arData["PROPERTIES"][$arProperty["ID"]]->SetData($arProperty);
                    }
                }
            }
        }

	    return $this;
    }

	/**
	 * @param FilterPropMetaData[] $filterPropsMetaData
	 * @return $this
	 */
	public function SetAdditionalPropsMetaData(array $filterPropsMetaData)
	{
		foreach($filterPropsMetaData as $prop){
			$propId = $prop->getId();
			/**
			 * @var CustomFilterProperty $filterProperty
			 */
			$filterProperty = $this->GetProperty($propId);
			if($filterProperty){
				$filterProperty->setMetaData($prop);
			}
		}

		return $this;
	}

    public function ParseRequest()
    {
        if(empty($_REQUEST[$this->sName])) return;
        foreach($_REQUEST[$this->sName] as $key=>$value)
        {
            $PropertyName = "";
            $Multiple = "";
            $IsOffer = false;
            $IsDifferent = false;
            if(is_object($this->GetProperty($key)))
            {
                $PropertyName	= $this->GetProperty($key)->GetParam("TITLE");
                $Multiple		= $this->GetProperty($key)->GetParam("MULTIPLE");
                $IsOffer		= $this->GetProperty($key)->IsOffer();
                $IsDifferent	= $this->GetProperty($key)->GetData("IBLOCK_ID") != $this->arParams["MAIN_IBLOCK"];
            }
            $this->GetSelectedValues()->Add($key, array("NAME" => $PropertyName, "MULTIPLE" => $Multiple, "IS_OFFER" => $IsOffer, "IS_DIFFERENT" => $IsDifferent), $value);
        }
    }

    public function Build()
    {
        if($this->inSEFMode()){
            /**
             * @var \Aniart\Main\Seo\CustomFilterSEFController $sefController
             */
            $sefController = $this->GetSEFController();
            if($sefController::determineSefUrl()){
                $sefController->parseSefUrlToGlobalRequest();
            }
        }
        $this->ParseRequest();

        if(!empty($this->arData["PROPERTIES"]))
        {
            foreach($this->arData["PROPERTIES"] as $propertyID=>$obProp)
            {
                /**
                 * @var CustomFilterProperty $obProp;
                 */
                if(!empty($_REQUEST[$this->sName][$propertyID])){
                    $obProp->AddSelectedValues($_REQUEST[$this->sName][$propertyID]);
                }

                if(!$obProp->IsHidden())
                {
                    $arParams = array_merge($this->GetParams(), array("FILTER" => $this, "PROPERTY" => $obProp, "FILTER_NAME" => $this->sName, "SELECTED_VALUES" => $this->GetSelectedValues()));
                    $filterType = $obProp->GetParam("TYPE") ? $obProp->GetParam("TYPE"):".default";

                    $html = $this->Execute($filterType, $arParams);
                    $obProp->SetHtml($html);
                }
                else {
                    $obProp->SetHtml("");
                }
            }
        }
    }

    public function ObtainFullBitrixFilter($CurrentPropertyID = array(), $ExcludePropertyFromFilter = false)
    {
        $arResult = array();
        $arFilter = $this->GetSelectedValues()->CreateBitrixFilter($CurrentPropertyID, $ExcludePropertyFromFilter);
        if(!empty($arFilter))
        {
            if(!isset($arFilter["MAIN=>MAIN"]))
            {
                foreach($arFilter as $FilterType=>$FilterData){
                    if($FilterType!=="MAIN=>OFFER" && $FilterType!=="OFFER=>MAIN"){
                        $arFilter["MAIN=>MAIN"][$FilterType] = $FilterData;
                        unset($arFilter[$FilterType]);
                    }
                }
            }

            foreach($arFilter as $FilterType => $FilterData)
            {
                switch($FilterType)
                {
                    case "OFFER=>MAIN":
                        $arSubQuery = array(
                            "ACTIVE"	=> "Y",
                            "IBLOCK_ID"	=> $this->arParams["OFFERS_IBLOCK_ID"]
                        );
                        $arSubQuery	= array_merge($arSubQuery, $FilterData);
                        $arID		= CIBlockElement::SubQuery("PROPERTY_".$this->arParams["OFFERS_PROPERTY_ID"], $arSubQuery);
                        if(!empty($arResult["ID"])){
                            $arResult[] = array("LOGIC"=>"AND", "ID" => $arID);
                        }
                        else $arResult["ID"] = $arID;
                        break;
                    case "MAIN=>OFFER":
                        $arrFilter = array(
                            "ACTIVE"	=> "Y",
                            "IBLOCK_ID"	=> $this->arParams["PRODUCTS_IBLOCK_ID"],
                        );
                        $arrFilter = array_merge($arrFilter, $FilterData);
                        $rsElements = CIBlockElement::GetList(array(), $arrFilter, false, false, array("ID"));
                        while($arElement = $rsElements->Fetch()){
                            $IDs[] = $arElement["ID"];
                        }
                        if(!empty($IDs))
                        {
                            if(!empty($arResult["ID"])){
                                $arResult[] = array("LOGIC"=>"AND", "PROPERTY_".$this->arParams["OFFERS_PROPERTY_ID"] => $IDs);
                            }
                            else $arResult["PROPERTY_".$this->arParams["OFFERS_PROPERTY_ID"]] = $IDs;
                        }
                        else $arResult["PROPERTY_".$this->arParams["OFFERS_PROPERTY_ID"]] = 0; //HACK !!! <------------------------------------------------------
                        break;
                    default:
                        if($FilterType !== "MAIN=>MAIN"){
                            $FilterData = array($FilterType => $FilterData);
                        }
                        $arResult = array_merge($arResult, $FilterData);
                        break;
                }
            }
        }
        return $arResult;
    }


    public function SortProperties(){
        if(!empty($this->arData['PROPERTIES'])){
            uasort($this->arData["PROPERTIES"], array($this, 'SortBySort'));
        }
    }

    //функция для сортировки
    private function SortBySort($a,$b)
    {
        if($a->GetParam("SORT") == $b->GetParam("SORT"))
            return 0;
        else
            return ($a->GetParam("SORT") < $b->GetParam("SORT")) ? -1 : 1;
    }


    //функция для удаления фильтра из общего массива фильтров по всей глубине вложенности
    public function RemoveFromFilter($key,&$arCurrentFilters)
    {
        if(!is_array($arCurrentFilters)) $arCurrentFilters = array();

        foreach($arCurrentFilters as $k=>&$v)
        {
            if(is_array($v)){
                $this->RemoveFromFilter($key, $v);
            }
            elseif($k == $key){
                unset($arCurrentFilters[$k]);
            }
        }
        unset($v);
    }

    //функция, которая определяет есть ли указанный фильтр в общем массиве фильтров по всей глубине вложенности
    public function FilterExists($key, $arCurrentFilters)
    {
        if(is_array($arCurrentFilters))
        {
            if(isset($arCurrentFilters[$key])) return true;
            else
            {
                foreach($arCurrentFilters as $k=>$v)
                {
                    if(is_array($v)){
                        return $this->FilterExists($key, $v);
                    }
                }
            }
        }
        else return;

        return false;
    }

    //функция, которая формирует общий массив фильтров с логикой ИЛИ в пределах одного фильтра
    public function AddSubFilter($prop_id, $prop_values, &$arFilters)
    {
        if(empty($prop_id) || empty($prop_values)) return;

        $property = "PROPERTY_".$prop_id;
        if(is_array($prop_values))
        {
            if(isset($prop_values["f"]) || isset($prop_values["t"]))
            {
                if(!empty($prop_values["f"]))
                    $arFilters[">=".$property] = $prop_values["f"];
                if(!empty($prop_values["t"]))
                    $arFilters["<=".$property] = $prop_values["t"];
            }
            else
            {
                if(count($prop_values) == 1)
                    $arFilters[$property] = current($prop_values);
                else
                {
                    $arrFilter = array("LOGIC" => "OR");
                    foreach($prop_values as $value)
                        $arrFilter[] = array($property => $value);

                    $arFilters[] = $arrFilter;
                }
            }
        }
        else $arFilters[$property] = $prop_values;
    }

    //функция для подключения фильтров
    public function Execute($filter_type, $arParams)
    {
        global $APPLICATION;
        $filter_type = strtolower(trim($filter_type));

        $path = "components/aniart/custom.filter.oop/filters/".$filter_type."/controller.php";
        if(!$return = $APPLICATION->IncludeFile($path, $arParams, array("SHOW_BORDER"=>false)))
        {
            $path = "/local/".$path;
            if(!$return = $APPLICATION->IncludeFile($path, $arParams, array("SHOW_BORDER"=>false)))
            {
                ShowError("Не найден обработчик 'controller.php' для фильтра '{$filter_type}'");
                return false;
            }
        }

        return $return;
    }
}

class CustomFilters
{
    private static $arCustomFilters = array();

    public static function IsExists($FilterName){
        return isset(self::$arCustomFilters[$FilterName]);
    }

    public static function Get($FilterName){
        return self::$arCustomFilters[$FilterName];
    }

    public static function Add($FilterName, $arParams = array())
    {
        if(!self::IsExists($FilterName)){
            self::$arCustomFilters[$FilterName] = new CustomFilter($FilterName, $arParams);
        }
        else{
            $Filter = self::$arCustomFilters[$FilterName];
            $Filter->SetParams($arParams);
            if($Filter->PropertiesCount() > 0)
            {
                foreach($Filter->GetProperties() as $Property){
                    $Property->Hide();
                }
            }
        }

        return self::$arCustomFilters[$FilterName];
    }
}
?>