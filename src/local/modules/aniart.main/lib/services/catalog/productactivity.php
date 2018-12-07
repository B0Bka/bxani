<?php


namespace Aniart\Main\Services\Catalog;

use Aniart\Main\Repositories\ProductsRepository;

class ProductActivity extends Service
{
    
    /**
     * @var ProductsRepository
     */
    private $productsRepository;
    
    protected $product;
    protected $productId;
    
    public function __construct($productId)
    {
        parent::__construct();
        $this->productsRepository = app('ProductsRepository');
        
        $this->productId = $productId;
        $this->product = $this->productsRepository->getById($this->productId);
    }
    
    public function init()
    {
        if(!$this->product->isActive())
        {
            return false;
        }
        if(!$this->check())
        {
            return false;
        }
        //deactive
        return $this->productsRepository->update($this->productId, [
            'ACTIVE' => 'N'
        ]);
    }
    
    protected function check()
    {
        $price = $this->product->getPrice();
        $images = $this->product->getPropertyValue(
            $this->product->getPropCode('more_photo')
        );
        if(empty($price) || $price <= 0)
        {
            return true;
        }
        if(empty($images))
        {
            return true;
        }
        return false;
    }

    public function getProduct()
    {
        if(is_null($this->product))
        {
            $this->product = $this->productsRepository->getById($this->productId);
        }
        return $this->product;
    }
    
    public function getProductId()
    {
        return $this->productId;
    }
    
    

}