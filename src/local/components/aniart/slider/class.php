<?php

use Aniart\Main\Exceptions\AniartException,
    Aniart\Main\Repositories\SliderRepository,
    Bitrix\Main\Security\Sign\Signer;

class CSliderComponent extends CBitrixComponent
{

    protected $sliderRepository;

    public function __construct($component = null)
    {
        parent::__construct($component);
    }
    
    public function onPrepareComponentParams($arParams)
    {
        $arParams['IBLOCK_TYPE'] = $arParams['IBLOCK_TYPE']?:'';
        $arParams['IBLOCK_ID'] = (int)$arParams['IBLOCK_ID']?:0;
        $arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE']?:'A';
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME']?:36000;

        return parent::onPrepareComponentParams($arParams);
    }
    
    public function executeComponent()
    {
        try
        {
            $this->doExecuteComponent();
        }
        catch(AniartException $e)
        {
            ShowError($e->getMessage());
        }
    }

    private function doExecuteComponent()
    {
        if($this->StartResultCache())
        {
        	$this->init();
	        $this->IncludeComponentTemplate();
        }
    }
    
    private function init()
    {
        $this->sliderRepository = new SliderRepository($this->arParams['IBLOCK_ID']);
        
        $this->arResult['ITEMS'] = $this->getSlides();
    }
    
    private function getSlides()
    {
        return $this->sliderRepository->getList(
            $this->getSort(), 
            $this->getFilter()
        );
    }
    
    private function getSort()
    {
        return [
            'SORT' => 'ASC', 
            'DATE_CREATED' => 'DESC'
        ];
    }
    
    private function getFilter()
    {
        return [
            'ACTIVE_DATE' => 'Y',
            'SECTION_ACTIVE' => 'Y',
            'ACTIVE' => 'Y'
        ];
    }
    
    private function getProducts()
    {
        $products = $this->getProductsFilter();
        if(empty($products))
        {
            return [];
        }
        $result = $this->productsRepository->getList(
            ['SORT' => 'ID'], 
            ['ID' => $products]
        );
        return $result;
    }
    
    private function getProductsFilter()
    {
        $result = [];
        $sliders = $this->arResult['ITEMS'];
        if(empty($sliders))
        {
            return $result;
        }
        foreach($sliders as $slider)
        {
            $products = $slider->getPropertyValue('PRODUCTS');
            if(empty($products))
            {
                continue;
            }
            foreach($products as $product)
            {
                $result[$product] = $product;
            }
        }
        return $result;
    }

    public function getSignedComponentParams()
	{
		$signer = new Signer();
		return $signer->sign(base64_encode(serialize($this->arParams)), 'slider');
	}
}

?>
