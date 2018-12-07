<?

use Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

CModule::IncludeModule("iblock");

class CAniartProductDetail extends CBitrixComponent
{

    /**
     * @var \Aniart\Main\Repositories\ProductsRepository
     */
    private $productRepositoryInstance;

    public function __construct($component = null)
    {
        $this->productRepositoryInstance = app("ProductsRepository");
        $this->basketItemsRepository = app('BasketItemsRepository');
        $this->basket = app('Basket', [['BASKET_ITEMS'=>$this->basketItemsRepository->getList(
            ['ID'=>'ASC'],
            [
                'FUSER_ID'=>CSaleBasket::GetBasketUserID(),
                'LID'=>SITE_ID,
                'ORDER_ID'=>'NULL'
            ]
        )]]);
        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams["ELEMENT_CODE"] = $_REQUEST['ELEMENT_CODE'] ?: trim($arParams['ELEMENT_CODE']);
        $arParams["OFFER_ID"] = $_REQUEST["OFFER_ID"];
        $arParams["COLLECTION_ID"] = $_REQUEST["collection"];

        return parent::onPrepareComponentParams($arParams);
    }

    public function executeComponent()
    {
        $this->arParams["CACHE_TIME"] = $this->arParams["CACHE_TIME"] ?: 3600;
        if(empty($this->arParams["ELEMENT_CODE"]))
        {
            $this->process404();
            return;
        }
		$this->arResult["BASKET_PRODUCTS_ID"] = $this->basket->getProductIds();
        if($this->startResultCache())
        {

            //Получить товар по коду
            $filter = ["CODE" => $this->arParams["ELEMENT_CODE"]];
            $productsByCode = $this->productRepositoryInstance->getList([], $filter);
            $product = reset($productsByCode);
            if(empty($product))
            {
                $this->abortResultCache();
                $this->process404();
                return;
            }
            elseif(!$product->isActive())//редирект в раздел, если товар не активен
            {
                $sections = $product->getSections();
                $section = reset($sections);
                $url = !empty($section) ? $section->getUrl() : SITE_DIR;
                LocalRedirect($url, false, "301 Moved permanently");
            }

            /**
             * @var \Aniart\Main\Models\Product $product
             */
            $this->arResult["PRODUCT"] = array_shift($productsByCode);
            $this->arResult["OFFERS"] = $product->getOffers();
            $this->arResult["CURRENT_OFFER"] = $this->arParams["OFFER_ID"] ? $this->arResult["OFFERS"][$this->arParams["OFFER_ID"]] : array_shift(array_values($this->arResult["OFFERS"]));

            if(empty($this->arResult))
            {
                $this->abortResultCache();
            }
            $this->sendBigData();
            $this->includeComponentTemplate();
        }
        $this->arResult["PRODUCT"]->setBreadcrumbs();
        $this->setSeo();
        if(Loader::includeModule('catalog') && Loader::includeModule('sale'))
        {
            \Bitrix\Catalog\CatalogViewedProductTable::refresh(
                    $this->arResult["PRODUCT"]->getId(), CSaleBasket::GetBasketUserID(), SITE_ID
            );
        }
    }

    private function setSeo()
    {
        seo()->setPageTitle($this->arResult["PRODUCT"]->getSeoPageTitle(), true);
        seo()->setMetaTitle($this->arResult["PRODUCT"]->getSeoPageTitle(), true);
        seo()->setDescription($this->arResult["PRODUCT"]->getSeoDescription(), true);
        seo()->setKeywords($this->arResult["PRODUCT"]->getSeoKeywords(), true);
    }

    private function process404()
    {
        \Bitrix\Iblock\Component\Tools::process404('', true, true, true);
    }

    private function sendBigData()
    {
        global $USER;
        $sections = $this->arResult["PRODUCT"]->getSections();
        $section = reset($sections);
        if(!empty($section))
            return false;
        
        $productData = array(
            'product_id' => $this->arResult["PRODUCT"]->getId(),
            'product_title' => $this->arResult["PRODUCT"]->getName(),
            'category_id' => $section->getId(),
            'category' => $section->getName(),
            'price' => $this->arResult["PRODUCT"]->getPrice(false),
            'currency' => 'UAH'
        );

        $counterData = array(
            'item' => base64_encode(json_encode($productData)),
            'user_id' => $USER->GetID(),
            'recommendation' => '',
            'v' => '2'
        );
        \Bitrix\Main\Analytics\Counter::sendData('ct', $counterData);
    }
}
