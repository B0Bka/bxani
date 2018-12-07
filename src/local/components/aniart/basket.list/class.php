<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Localization\Loc as Loc;

class BasketList extends CBitrixComponent
{
    protected $basket;
    protected $basketItemsRepository;
    
    protected $cacheKeys = [];
    protected $cacheAddon = [];
    
    protected $post;
    protected $get;
    protected $request;
    
    public function __construct($component = null)
    {
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

    /**
     * Override component settings
     * 
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $result = $arParams;
        return $result;
    }
    
    /**
     * Required parameters for filling
     * 
     * @throws Main\ArgumentNullException
     */
    protected function checkParams()
    {
        $result = [
            'AJAX_MOD' => isset($arParams['AJAX_MOD']) ? $arParams['AJAX_MOD'] : '',
            'CACHE_TYPE' => isset($arParams['CACHE_TYPE']) ? $arParams['CACHE_TYPE'] : 'N',
            'CACHE_TIME' => isset($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : 36000000,
        ];
        return $result;
    }

    protected function checkJS()
    {
        $name = '/script.js';
        return Asset::getInstance()->addJs($this->getPath().$name);
    }

    /**
     * Connection of necessary modules
     * 
     * @throws Main\LoaderException
     */
    protected function checkModules()
    {
        if(!Loader::includeModule('iblock'))
        {
            throw new Main\LoaderException(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
        }
    }

    /**
     * Abort cache
     */
    protected function abortDataCache()
    {
        $this->AbortResultCache();
    }

    /**
     * Reading data from the cache or not
     * 
     * @return bool
     */
    protected function readDataFromCache()
    {
        if($this->arParams['CACHE_TYPE'] == 'N')
            return false;
        
        return !($this->StartResultCache(false, $this->cacheAddon));
    }
    
    /**
     * Array keys for caching arResult
     */
    protected function putDataToCache()
    {
        if(is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0)
        {
            $this->SetResultCacheKeys($this->cacheKeys);
        }
    }

    /**
     * Init ajax
     */
    protected function ajaxExecuteComponent()
    {
        $result = 'null';
        $function = $this->getPostFunction();
        if(!empty($function) && method_exists($this, $function))
        {
            $result = json_encode($this->{$function}());
        }
        die($result);
    }
     
    /**
     * Performs actions before caching 
     */
    protected function executeProlog()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->request = $_REQUEST;
        
        if($this->arParams['AJAX_MOD'] == 'Y')
        {
            return $this->ajaxExecuteComponent();
        }
        $this->cacheAddon = [];
    }
    
    /**
     * Main logic
     */
    protected function getResult()
    {
        $this->init();
        $this->arResult = $this->basket;
        
        return $this->arResult;
    }
    
    /**
     * Execute action after the component
     */
    protected function executeEpilog()
    {
        return false;
    }
    
    protected function init()
    {
        
    }

    protected function getUserId()
    {
        global $USER;
        if(!is_object($USER))
        {
            $USER = new \CUser;
        }
        return IntVal($USER->GetID());
    }
    
    protected function getPostFunction()
    {
        return $this->post['func'];
    }
    
    protected function setError($data)
    {
        return ['status' => 'error', 'data' => $data];
    }

    protected function setOK($data)
    {
        return ['status' => 'success', 'data' => $data];
    }

    /**
     * Init component
     */
    public function executeComponent()
    {
        try
        {
            $this->checkModules();
            $this->checkParams();
            $this->executeProlog();
            if(!$this->readDataFromCache())
            {
                $this->getResult();
                $this->putDataToCache();
                $this->includeComponentTemplate();
            }
            $this->executeEpilog();
        }
        catch (Exception $e)
        {
            $this->abortDataCache();
            ShowError($e->getMessage());
        }
    }
}
