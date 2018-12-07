<?php
namespace Aniart\Main\Repositories;

abstract class AbstractOrmRepository
{
    protected $dbResult;
    protected $nav;
    protected $cacheMetadata;

    abstract public function newInstance(array $fields = array());

    public function getMetadata($iblockId)
    {
        if (empty($iblockId)) return false;
        \Bitrix\Main\Loader::includeModule('iblock');
        $result = array();
        $obCache = new \CPHPCache;
        $cacheDir = '/' . $iblockId;
        if ($this->cacheMetadata && $obCache->InitCache(3600, 'iblockOrm', $cacheDir)) {
            $result = $obCache->GetVars();
        } else {
            $result['iblock'] = \Bitrix\Iblock\IblockTable::getRowById($iblockId);
            $result['props'] = array();
            $rs = \Bitrix\Iblock\PropertyTable::getList(array('filter' => array(
                'IBLOCK_ID' => $iblockId
            )));
            while ($arProp = $rs->fetch()) {
                $result['props'][$arProp['CODE']] = $arProp;
            }
            if ($this->cacheMetadata) {
                $obCache->StartDataCache();
                $obCache->EndDataCache($result);
            }
        }
        return $result;
    }

    public function getList($table, $params, $iblockId)
    {
        $iblockData = $this->getMetadata($iblockId);
        $navigation = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
        $navigation->allowAllRecords(true)
           ->setPageSize($params['limit'])
           ->initFromUri();
        $elements = $table::getList($params);
        while($arElement = $elements->fetch())
        {
            $arElement["~DETAIL_PAGE_URL"] = \CIBlock::ReplaceDetailUrl($iblockData['iblock']['DETAIL_PAGE_URL'], $arElement, true, 'E');
            $arElement["DETAIL_PAGE_URL"] = htmlspecialcharsbx($arElement["~DETAIL_PAGE_URL"]);
            $result[] =  $this->newInstance($arElement);
        }
        if(!empty($navigation)) $navigation->setRecordCount($elements->getCount());

        $this->dbResult = $elements;
        $this->nav = $navigation;

        return $result;
    }

    public function getLastDBResult()
    {
        return $this->dbResult;
    }

    public function getLastNav()
    {
        return $this->nav;
    }
}
