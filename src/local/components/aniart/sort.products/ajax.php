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
            $result = array_map(function(Product $product) use ($elite, $basketProductIds)
            {
                $offers = $product->getOffers();
                $sizes = [];
                foreach($offers as $offer)
                {
                    $sizes[] = [
                        'offerId'=>$offer->getId(),
                        'value'=>$offer->getSize(),
                        'inBasket' => (in_array($offer->getId(), $basketProductIds) ? true: ''),
                    ];
                }
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'url' => $product->getDetailPageUrl(),
                    'img' => ($elite == 'Y' ? $product->getMorePhotoElite(2) : $product->getMorePhoto(2)),
                    'sizes' => $sizes,
                    'color' => $product->getClothData(),
                    'priceDiscount' => $product->getPrice(true),
                    'price' => $product->getBasePrice(true),
                    'isDiscount' => ($product->hasDiscount() ? 'Y': 'N')
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