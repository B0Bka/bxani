<?php

use Aniart\Main\Models\Product;


class AniartSearchListComponent extends CBitrixComponent
{
    /**
     * @var  \Aniart\Main\Repositories\ProductsRepository;
     */
    protected $productsRepository;
	/**
	 * @var \Bitrix\Main\HttpRequest
	 */
    protected $request;

    public function __construct($component = null)
    {
        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE'] ?: 'A';
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ?: 36000;
        $arParams['FILTER'] = array_merge((array)$arParams['FILTER'], [
            'ACTIVE' => 'Y'
        ]);

        return parent::onPrepareComponentParams($arParams);
    }

    public function executeComponent()
    {
		return $this->getSearchFilter();
    }
    private function getSearchFilter(){

        \Bitrix\Main\Loader::includeModule('search');

        $obSearch = new CSearch;
        $obSearch->SetOptions(array(
            'ERROR_ON_EMPTY_STEM' => false,
        ));

        $query = ltrim($_REQUEST["q"]);
        $arResult["alt_query"] = "";

        $arLang = CSearchLanguage::GuessLanguage($query);
        if (is_array($arLang) && $arLang["from"] != $arLang["to"])
            $arResult["alt_query"] = CSearchLanguage::ConvertKeyboardLayout($query, $arLang["from"], $arLang["to"]);

        $arResult["query"] = $query;
        $arResult["phrase"] = stemming_split($query, LANGUAGE_ID);

        $str_query = $arResult["alt_query"] ? $arResult["alt_query"] : $arResult["query"];

        $obSearch->Search(array(
            'QUERY' => $str_query,
            'SITE_ID' => SITE_ID,
            'MODULE_ID' => 'iblock',
            'PARAM2' => PRODUCTS_IBLOCK_ID
        ));


        if (!$obSearch->selectedRowsCount()) {
            $obSearch->Search(array(
                'QUERY' =>  $str_query,
                'SITE_ID' => SITE_ID,
                'MODULE_ID' => 'iblock',
                'PARAM2' => PRODUCTS_IBLOCK_ID
            ), array(), array('STEMMING' => false));
        }
        $arSearchFilter = array();
        while ($row = $obSearch->fetch()) {
            $arSearchFilter['=ID'][] = $row['ITEM_ID'];
        }
        if(!isset($arSearchFilter['=ID'])){
	        $arSearchFilter['=ID'] = 'empty';
        }
        return $arSearchFilter;
    }

	public function getSignedComponentParams()
	{
		$signer = new \Bitrix\Main\Security\Sign\Signer();
		return $signer->sign(base64_encode(serialize($this->arParams)), 'catalog.search');
	}
}