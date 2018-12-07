<?php

use Aniart\Main\Models\Product,
    Aniart\Main\FavoritesTable;

class UserFavoritesListComponent extends CBitrixComponent
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

        $this->productsRepository = app('ProductsRepository');
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE'] ?: 'A';
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ?: 3600;
        $arParams['FILTER'] = [
            'ACTIVE' => 'Y',
            'ID' => $this->getProductsIds()
        ];
        $arParams['SORT'] = $arParams['SORT'] ?: ['DATE_CREATED' => 'DESC'];
        $arParams['PAGE_VAR'] = $arParams['PAGE_VAR'] ?: 'page';
        $arParams['PAGE_SIZE'] = $arParams['PAGE_SIZE'] ?: 20;
        $arParams['PAGE_NUM'] = $this->request->get($arParams['PAGE_VAR']) ?: 1;
        $arParams['SORT_VAR'] = $arParams['SORT_VAR'] ?: 'sort';

        return parent::onPrepareComponentParams($arParams);
    }

    public function executeComponent()
    {
        try {
            $this->doExecuteComponent();
        } catch (\Aniart\Main\Exceptions\AniartException $e) {
            ShowError($e->getMessage());
        }
    }

    private function doExecuteComponent()
    {
        $this->initSortParams();
        if ($this->StartResultCache()) {
            $this->initProducts();
            $this->IncludeComponentTemplate();
        }
    }

    private function initSortParams()
    {
        $sortParams = $this->arParams['SORT_DATA'];
        $currentSort = $this->request->get($this->arParams['SORT_VAR']);
        $currentSort = $currentSort ?: 'default';
        foreach ($sortParams as $type => &$params) {
            if ($currentSort == $type) {
                $params['active'] = true;
                $this->currentSort = $type;
                $this->arParams['SORT'] = $params['order'];
                break;
            }
        }
        unset($params);
        $this->sortParams = $sortParams;
    }

    public function getSortParams()
    {
        return $this->sortParams;
    }

    public function getSortLink($type)
    {
        global $APPLICATION;
        $sort = '';
        if ($type !== 'default') {
            $sort = $this->currentSort === $type ? '' : 'sort=' . $type;
        }
        $link = $APPLICATION->GetCurPageParam($sort, ['sort']);
        return $link;
    }

    private function getProductsIds()
    {
        global $USER;
        $arResult = FavoritesTable::getProductIds(CUser::GetID());

	//dBug($arResult);
        return (count($arResult) > 0) ? $arResult : false;
    }

    /**
     * @return Product[]
     */
    private function initProducts()
    {

        $products = $this->productsRepository->getList(
            $this->arParams['SORT'],
            $this->arParams['FILTER'],
            false,
            [
                'iNumPage' => $this->arParams['PAGE_NUM'],
                'bShowAll' => false,
                'nPageSize' => $this->arParams['PAGE_SIZE']
            ]
        );
        //$this->setCollections($products);
        $this->arResult['PRODUCTS'] = array_values($products);

        if ($dbResult = $this->productsRepository->getLastDBResult()) {
            $this->arResult['DB_RESULT'] = $dbResult;
            $this->arResult['PAGINATION'] = [
                'NavNum' => $dbResult->NavNum, //порядковый номер постранички на странице
                'NavPageCount' => $dbResult->NavPageCount, //количество страниц
                'NavPageNomer' => $dbResult->NavPageNomer, //номер текущей страницы
                'NavPageSize' => $dbResult->NavPageSize, //количество элементов на странице
                'NavRecordCount' => $dbResult->NavRecordCount, //количество элементов в базе,
                'NavPageVar' => $this->arParams['PAGE_VAR']
            ];
        }
        $this->setModels($products);
    }

    private function setModels(array $products)
    {
        $modelsId = array_map(function(Product $product){
            return $product->getModel();
        }, $products);

        foreach($products as $product)
        {
            $sibling = $this->productsRepository->getProductsModelId($product->getModel());
            if(empty($sibling))
            {
                $sibling[] = $product;
            }
            $product->setSibling($sibling);
        }
    }

    private function setCollections(array $products)
    {
        $collectionsId = array_map(function (Product $product) {
            return $product->getCollectionId();
        }, $products);
        $collections = app('CollectionsRepository')->getList([], ['ID' => $collectionsId]);
        foreach ($products as $product) {
            $collectionId = $product->getCollectionId();
            if (!isset($collections[$collectionId])) {
                continue;
            }
            $product->setCollection($collections[$collectionId]);
        }
    }

    public function getSignedComponentParams()
    {
        $signer = new \Bitrix\Main\Security\Sign\Signer();
        return $signer->sign(base64_encode(serialize($this->arParams)), 'user.favorites');
    }
}