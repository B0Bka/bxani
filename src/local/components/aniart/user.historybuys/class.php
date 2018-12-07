<?php

use Aniart\Main\Models\Product,
    Aniart\Main\Models\Basket,
    Aniart\Main\Models\Order;


class AniartOrderProductListComponent extends CBitrixComponent
{
    /**
     * @var  \Aniart\Main\Repositories\ProductsRepository;
     */
    protected $productsRepository;
    protected $basketItemsRepository;
    protected $orderRepository;
    protected $arRecommended;
    protected $offerRepository;
	/**
	 * @var \Bitrix\Main\HttpRequest
	 */
    protected $request;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->basketItemsRepository = app('BasketItemsRepository');
        $this->productsRepository = app('ProductsRepository');
        $this->orderRepository = app('SaleOrdersRepository');
        $this->offerRepository = app('OffersRepository');
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE'] ?: 'A';
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ?: 36000;

	    $arParams['PAGE_VAR'] = $arParams['PAGE_VAR'] ?: 'page';
        $arParams['PAGE_SIZE'] = $arParams['PAGE_SIZE'] ?: 20;
        $arParams['PAGE_NUM'] = $this->request->get($arParams['PAGE_VAR']) ?: 1;

        return parent::onPrepareComponentParams($arParams);
    }


    public function executeComponent()
    {
        try{
            $this->doExecuteComponent();
        }
        catch(\Aniart\Main\Exceptions\AniartException $e){
            ShowError($e->getMessage());
        }
    }

    /**
     * @return mixed
     * obtain offerss from basket
     */
    private function getBasketItems(){
        global $USER;
        $retailCrm = new Aniart\Main\Tools\retailCrmHelper();
        if($retailCrm->isDev())
            return false;
        
        $resultItems = $retailCrm->getUserBuyedItems($USER->GetID());
        $this->arResult['RETAIL_PRODUCTS'] = $resultItems;
        $ids = array_keys($resultItems);
        if(!empty($ids))
        {
            $arFilter = array('=ID' => $ids);
            $offers = $this->offerRepository->getList(
                Array("SORT" => "ASC"),
                $arFilter,
                false,
                false
            );
            foreach ($offers as $offer) $this->arResult['OFFERS'][$offer->getId()] = $offer->getProductId();
            $this->arResult['OFFER'] = $offers;
            $arFilter = array('=ID' => $this->arResult['OFFERS']);
            $products = $this->productsRepository->getList(
                Array("SORT" => "ASC"),
                $arFilter,
                false,
                [
                    'iNumPage' => $this->arParams['PAGE_NUM'],
                    'bShowAll' => false,
                    'nPageSize' => $this->arParams['PAGE_SIZE']
                ]
            );
            $this->arResult['PRODUCT'] = $products;
        }
        else $this->arResult['PRODUCT'] = [];
        if($dbResult = $this->productsRepository->getLastDBResult())
        {
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
    }

    private function doExecuteComponent()
    {

        if ($this->StartResultCache()) {
            $this->getBasketItems();
        	$this->initProducts();
	        $this->IncludeComponentTemplate();
        }
    }


    /**
     * @return Product[]
     */
    private function initProducts()
    {
        foreach ($this->arResult['PRODUCT'] as $key => $product){
            $this->arResult['RECOMMENT_IDS'][$key] = $product->getRecomendedProduct();
        }
        $this->getRecommendedProducts();
    }

   private function getRecommendedProducts(){
        $arResult = [];
        foreach ($this->arResult['RECOMMENT_IDS'] as $arItem){
            if(is_array($arItem)) $arResult = array_merge($arItem,$arResult);
        }
        $arResult = array_unique($arResult);
        $arFilter = array('=ID' => $arResult);

       $products = $this->productsRepository->getList(
           Array("SORT" => "ASC"),
           $arFilter,
           false,
           false
       );
        $this->arResult['RECOMMEND_PRODUCTS'] = $products;
        //$this->setModels($products);
   }
    private function setModels(array $products)
	{
	    AddMessage2Log($products);
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
		$collectionsId = array_map(function(Product $product){
			return $product->getCollectionId();
		}, $products);
		$collections = app('CollectionsRepository')->getList([], ['ID' => $collectionsId]);
		foreach($products as $product){
			$collectionId = $product->getCollectionId();
			if(!isset($collections[$collectionId])){
				continue;
			}
			$product->setCollection($collections[$collectionId]);
		}
	}

	public function getSignedComponentParams()
	{
		$signer = new \Bitrix\Main\Security\Sign\Signer();
		return $signer->sign(base64_encode(serialize($this->arParams)), 'products.list');
	}
}