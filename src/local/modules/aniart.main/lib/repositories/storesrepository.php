<?php


namespace Aniart\Main\Repositories;


class StoresRepository extends AbstractStoresRepository
{
    public function newInstance(array $fields = [])
    {
        return app('Store', [$fields]);
    }

    public function getList($arOrder = ['SORT' => 'ASC'], $arFilter = [], $arGroupBy = false, $arNavStartParams = false, $arSelectedFields = [])
    {
        $stores = [];
        if (empty($arSelectedFields))
        {
            $arSelectedFields = [
                "*",
                "UF_*"
            ];
        }
        $rsStores = $this->dbResult = \CCatalogStore::GetList(
            $arOrder, 
            $arFilter, 
            $arGroupBy, 
            $arNavStartParams, 
            $arSelectedFields
        );
        while($arStore = $rsStores->GetNext())
        {
            $stores[] = $this->newInstance($arStore);
        }
        return $stores;
    }
}