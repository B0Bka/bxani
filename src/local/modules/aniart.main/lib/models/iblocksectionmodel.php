<?php 
namespace Aniart\Main\Models;

use Aniart\Main\Interfaces\SeoParamsInterface;
use Bitrix\Iblock\InheritedProperty\SectionValues;

class IblockSectionModel extends AbstractModel implements SeoParamsInterface
{
    protected $seoParams;

	public function getName()
	{
		return $this->fields['NAME'];
	}
	
	public function getUrl() {
		return $this->fields['SECTION_PAGE_URL'];
	}

    public function getCode()
    {
        return $this->fields['CODE'];
    }

	public function getXMLId()
	{
		return $this->fields['XML_ID'];
	}
	
	public function getIblockId()
	{
		return $this->fields['IBLOCK_ID'];
	}
    
    public function getParentId()
	{
		return $this->fields['IBLOCK_SECTION_ID'];
	}

    public function getDepthLevel()
	{
		return $this->fields['DEPTH_LEVEL'];
	}
	public function getPropertyValue($propName, $index = false)
	{
		$result = false;
		if(!empty($propName)){
			$propValue = $this->{'UF_'.$propName};
			$propValue = $propValue ? $propValue : $this->fields['PROPERTIES'][$propName]['VALUE'];
			if($propValue && $index !== false){
				$propValue = $propValue[$index];
			}
			$result = $propValue;
		}
		return $result;
	}
	
	public function getPreviewPictureId()
	{
		return $this->fields['PREVIEW_PICTURE'];
	}

    public function getSeoPageTitle()
    {
        return $this->getSeoParamValue('SECTION_PAGE_TITLE');
    }

    public function getSeoMetaTitle()
    {
        return $this->getSeoParamValue('SECTION_META_TITLE');
    }

    public function getSeoKeywords()
    {
        return $this->getSeoParamValue('SECTION_META_KEYWORDS');
    }

    public function getSeoDescription()
    {
        return $this->getSeoParamValue('SECTION_META_DESCRIPTION');
    }

	protected function getSeoParamValue($paramName)
	{
		$this->getSeoParams();
		return $this->seoParams[$paramName];
	}

	protected function getSeoParams()
	{
		if(is_null($this->seoParams)){
			$this->obtainSeoParams();
		}
		return $this->seoParams;
	}

	protected function obtainSeoParams()
	{
		$seoParamsValues = array();
		if(($iblockId = $this->getIblockId()) && ($id = $this->getId())){
			$seoParams = new SectionValues($iblockId, $id);
			if($seoParams){
				$seoParamsValues = $seoParams->getValues();
			}
		}
		$this->seoParams = $seoParamsValues;

		return $this;
	}
    
    public function getPageTitle(){
        return $this->getSeoParamValue('SECTION_PAGE_TITLE');
    }

    public function getMetaTitle(){
        return $this->getSeoParamValue('SECTION_META_TITLE');
    }

    public function getKeywords(){
        return $this->getSeoParamValue('SECTION_META_KEYWORDS');
    }

    public function getDescription(){
        return $this->getSeoParamValue('SECTION_META_DESCRIPTION');
    }

    public function getPropertyListValue($propName){
		if(!empty($propName)){
			$propValue = $this->{'UF_'.$propName};
			$propValue = $propValue ? $propValue : $this->fields['PROPERTIES'][$propName]['VALUE'];
			if(!empty($propValue)){
                $rsVal = \CUserFieldEnum::GetList(array(), array("ID" => $propValue));
                if ($arProp = $rsVal->GetNext()) return $arProp["VALUE"];
            }
		}
        return false;
    }

    public function getAplication()
    {
        global $APPLICATION;
        return $APPLICATION;
    }

    public function setBreadcrumbs()
    {
        foreach($this->getNavChain() as $key => $arSection)
        {
            $this->getAplication()->AddChainItem($arSection["NAME"], $arSection["SECTION_PAGE_URL"]);
        }
    }

    public function getNavChain()
    {
        $arResult = [];
        $arSelect = [
            "ID",
        ];
        $dbList = \CIBlockSection::GetNavChain($this->getIblockId(), $this->getId(), $arSelect);
        while($arItem = $dbList->GetNext())
        {
            $arId[] = $arItem["ID"];
        }

        if($arId)
        {
            $dbSectionList = \CIBlockSection::GetList([], ["IBLOCK_ID" => $this->getIblockId(), "ID" => $arId], false, ["ID", "NAME", "UF_NAME_".strtoupper(i18n()->lang()), "SECTION_PAGE_URL"]);
            while($arSection = $dbSectionList->GetNext())
            {
                $arResult[$arSection["ID"]]["ID"] = $arSection["ID"];
                $name = i18nSelect($arSection["NAME"], $arSection["UF_NAME_".strtoupper(i18n()->lang())]);
                $arResult[$arSection["ID"]]["NAME"] = (strlen($name) > 0) ? $name : $arSection["NAME"];
                $arResult[$arSection["ID"]]["SECTION_PAGE_URL"] = $this->getId() != $arSection["ID"] ? i18n()->getLangDir($arSection["SECTION_PAGE_URL"]) : '';
            }
        }
        return $arResult;
    }
}