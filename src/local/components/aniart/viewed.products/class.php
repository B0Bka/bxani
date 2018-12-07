<?
use Aniart\Main\Models\Product,
    Bitrix\Catalog\CatalogViewedProductTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

CModule::IncludeModule("iblock");

class CAniartViewedProducts extends CBitrixComponent
{
    /**
     * @var \Aniart\Main\Repositories\ProductsRepository
     */
    private $productRepositoryInstance;


    public function __construct($component = null)
    {
        $this->productRepositoryInstance = app("ProductsRepository");

        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams['PAGE_ELEMENT_COUNT'] = 6;

        return parent::onPrepareComponentParams($arParams);
    }

    public function executeComponent()
    {
        $this->arParams["CACHE_TIME"] = $this->arParams["CACHE_TIME"] ?: 3600;

        $elementsID = [];

        $filter = array('=FUSER_ID' => CSaleBasket::GetBasketUserID(), '=SITE_ID' => SITE_ID);
        $dbl = CatalogViewedProductTable::getList(array(
            'select' => array('PRODUCT_ID', 'ELEMENT_ID'),
            'filter' => $filter,
            'order' => array('DATE_VISIT' => 'DESC'),
            'limit' => $this->arParams['PAGE_ELEMENT_COUNT']
        ));

        while ($res = $dbl->Fetch()) {
            $this->arParams["ELEMENT_ID"][] = $res["ELEMENT_ID"];
        }

        if ($this->startResultCache()) {

            $sort = ["SORT" => "ASC"];
            $filter = ["ID" => $this->arParams["ELEMENT_ID"]];
            $products = $this->productRepositoryInstance->getList($sort, $filter);
            $this->arResult["ITEMS"] = $products;
            $this->setModels($products);
            //dBug($this->arResult["ITEMS"]);
            //die;
            if (empty($this->arResult["ITEMS"])) {
                $this->abortResultCache();
            }
            $this->includeComponentTemplate();

            $this->endResultCache();
        }
    }

    private function setModels(array $products)
    {
        $modelsId = array_map(function(Product $product){
            return $product->getModel();
        }, $products);

        foreach($products as $product)
        {
            //dBug($product->getModel());
            $sibling = $this->productRepositoryInstance->getProductsModelId($product->getModel());
            if(empty($sibling))
            {
                $sibling[] = $product;
            }
            $product->setSibling($sibling);
        }
    }
    public function getSignedComponentParams()
    {
        $signer = new \Bitrix\Main\Security\Sign\Signer();
        return $signer->sign(base64_encode(serialize($this->arParams)), 'viewed.products');
    }
}