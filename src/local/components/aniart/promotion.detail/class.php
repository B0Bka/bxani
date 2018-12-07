<?php

use Aniart\Main\Models\Product;

class AniartPromotionDetailComponent extends CBitrixComponent
{
    protected $promotionsRepository;
    protected $productsRepository;

    public function __construct($component = null)
    {
        parent::__construct($component);

        $this->promotionsRepository = app('PromotionsRepository');
        $this->productsRepository = app('ProductsRepository');
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE'] ?: 'A';
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ?: 36000;
        return parent::onPrepareComponentParams($arParams);
    }

    public function executeComponent()
    {
        try
        {
            $this->doExecuteComponent();
        }
        catch(\Aniart\Main\Exceptions\AniartException $e)
        {
            ShowError($e->getMessage());
        }
    }

    private function doExecuteComponent()
    {
        if($this->StartResultCache())
        {
            $this->initPromotion();
	        $this->IncludeComponentTemplate();
        }
    }

    private function initPromotion()
    {
        $this->arResult['PROMOTION'] = $this->promotionsRepository->getByCode($this->arParams['ELEMENT_CODE']);
        $this->arResult['ITEMS'] = $this->arResult['PROMOTION']->getItems();
        $this->arResult['PROMOTION']->setBreadcrumbs();
    }
}