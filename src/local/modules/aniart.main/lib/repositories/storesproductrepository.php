<?php


namespace Aniart\Main\Repositories;

class StoresProductRepository extends AbstractStoresRepository
{
    
    public function newInstance(array $fields = [])
    {
        return app('StoreProduct', [$fields]);
    }

    public function getList($arOrder = ['SORT' => 'ASC'], $arFilter = [], $arGroupBy = false, $arNavStartParams = false, $arSelectedFields = [])
    {
        $stores = [];
        if (empty($arSelectedFields))
        {
            $arSelectedFields = ['*'];
        }
        $rsStores = $this->dbResult = \CCatalogStoreProduct::GetList(
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