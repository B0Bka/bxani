<?php

namespace Aniart\Main\Ajax\Handlers;

use Aniart\Main\Ajax\AbstractAjaxHandler,
    Aniart\Main\Models\Product,
    Bitrix\Main\Security\Sign\BadSignatureException;

class ProductsListAjaxHandler extends AbstractAjaxHandler
{
    
    /**
     * @var  \Aniart\Main\Repositories\ProductsRepository;
     */
    protected $productsRepository, $basketItemsRepository;
    protected $basket;

    public function __construct()
    {
        parent::__construct();
        
        $this->productsRepository = app('ProductsRepository');
        $this->basketItemsRepository = app('BasketItemsRepository');
        $this->basket = app('Basket', [['BASKET_ITEMS'=>$this->basketItemsRepository->getList(
            ['ID'=>'ASC'],
            [
                'FUSER_ID'=>\CSaleBasket::GetBasketUserID(),
                'LID'=>SITE_ID,
                'ORDER_ID'=>'NULL'
            ]
        )]]);
    }
    
    public function getProduct()
    {
		try
        {
            $productId = (int)$this->post['product'];
            $elite = $this->post['elite'];
            if($productId <= 0)
            {
                return $this->setError("Wrong product id {$productId}");
            }
			$componentParams = $this->getComponentParamsFromRequest('products.list', 'componentParams');
            $filter = [
                'IBLOCK_ID' => $componentParams['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                'ID' => $productId
            ];
            $productData = $this->productsRepository->getList([], $filter);
            $basketProductIds = $this->basket->getProductIds();
            if (reset($productData)->showSizeTypes())//выводить для товаров варианты размеров
            {
                $sizeRepository = app('SizesRepository');
                $arSizesList = $sizeRepository->getList([],['!UF_NAME_RU' => false]);
                foreach ($arSizesList as $sizeItem)
                {
                    $arSizes[$sizeItem->getName()]['name_ua'] = $sizeItem->getNameUa();
                    $arSizes[$sizeItem->getName()]['name_int'] = $sizeItem->getNameInt();
                }
            }
            $result = array_map(function(Product $product) use ($elite, $basketProductIds, $arSizes)
            {
                $offers = $product->getOffers();
                $sizes = [];
                foreach($offers as $offer)
                {
                    $size = $offer->getSize();
                    $sizes[$size] = [
                        'offerId' => $offer->getId(),
                        'value' => $size,
                        'inBasket' => (in_array($offer->getId(), $basketProductIds) ? true: ''),
                    ];
                    if ($product->showSizeTypes())
                    {
                        $sizes[$size]['name_ua'] = $arSizes[$size]['name_ua'];
                        $sizes[$size]['name_int'] = $arSizes[$size]['name_int'];
                        $sizes[$size]['name_eu'] = $size;
                    }
                }
                ksort($sizes);
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'url' => $product->getDetailPageUrl(),
                    'img' => ($elite == 'Y' ? $product->getMorePhotoElite(2) : $product->getMorePhoto(2)),
                    'sizes' => array_values($sizes),
                    'color' => $product->getClothData(),
                    'priceDiscount' => $product->getPrice(true),
                    'price' => $product->getBasePrice(true),
                    'isDiscount' => ($product->hasDiscount() ? 'Y': 'N'),
                    'inFavorites' => $product->isInFav($product->getId()),
                    'available' => !empty($product->getAvailable())? 'Y' : 'N'
                ];
            }, $productData);
			return $this->setOK($result);
		}
		catch (BadSignatureException $e)
        {
			return $this->setError($e->getMessage());
		}
    }
    
    

    protected function getComponentParamsFromRequest($salt = 'products.list', $requestKey = 'signedParamsString')
	{
		return parent::getComponentParamsFromRequest($salt, $requestKey);
	}

}