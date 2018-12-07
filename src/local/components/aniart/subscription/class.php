<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Localization\Loc as Loc;

class Subscribs extends CBitrixComponent
{

    /**
     * Кешируемые ключи arResult
     *
     * @var array
     */
    protected $cacheKeys = array();
    
    /**
     * Дополнительные параметры кеш
     *
     * @var array
     */
    protected $cacheAddon = array();

    /**
     * Переопределяет параметры компонента
     * 
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            'ID' => $arParams['ID'],
            'AJAX' => isset($arParams['AJAX']) ? $arParams['AJAX'] : '',
            'DATA' => isset($arParams['DATA']) ? $arParams['DATA'] : array(),
            'CACHE_TYPE' => isset($arParams['CACHE_TYPE']) ? $arParams['CACHE_TYPE'] : 'N',
            'CACHE_TIME' => isset($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : 36000000,
        );
        return $result;
    }
    
    /**
     * Проверяет заполнение обязательных параметров
     * 
     * @throws Main\ArgumentNullException
     */
    protected function checkParams()
    {
        if(empty($this->arParams['ID']))
            throw new Main\ArgumentNullException('ID');
    }

    protected function checkJS()
    {
        $name = '/script.js';
        return Asset::getInstance()->addJs($this->getPath().$name);
    }

        /**
     * Проверяет подключение необходиимых модулей
     * 
     * @throws Main\LoaderException
     */
    protected function checkModules()
    {
        if(!Main\Loader::includeModule('subscribe'))
        {
            throw new Main\LoaderException(Loc::getMessage('STANDARD_MODULE_NOT_INSTALLED'));
        } 
    }

    /**
     * Прерывает кеширование
     */
    protected function abortDataCache()
    {
        $this->AbortResultCache();
    }

    /**
     * Определяет читать данные из кеша или нет
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
     * Кеширует ключи массива arResult
     */
    protected function putDataToCache()
    {
        if(is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0)
        {
            $this->SetResultCacheKeys($this->cacheKeys);
        }
    }
    
    /**
     * Основная логика
     */
    protected function getResult()
    {
        global $USER;
        $email = $USER->GetEmail();
        $subEmail = CSubscription::GetList(
            array(),
            array('EMAIL' => $email)
        );
        return $this->arResult['DATA'] = $subEmail->Fetch();
    }
    
    /**
     * Добавляет новую подписку
     * Активирует подписку
     * Отправляет письмо подтверждения
     * 
     * @global object $USER
     * @return array
     */
    protected function addSubscribe()
    {
        global $USER;
        $subscribe = new CSubscription;
        $userId = $USER->IsAuthorized() ? $USER->GetID() : false;
        $result = array(
            'type' => 'add',
            'status' => 'error'
        );
        $fields = array(
            'USER_ID' => $userId,
            'FORMAT' => 'html',
            'EMAIL' => $this->arParams['DATA']['EMAIL'],
            'ACTIVE' => 'Y',
            'RUB_ID' => $this->arParams['ID'],
            'SEND_CONFIRM' => 'N',
            'CONFIRMED' => 'Y'
        );
        $id = $subscribe->Add($fields);
        if($id > 0) {
			$subscribe = new Aniart\Main\Tools\retailCrmHelper;
            $newSubscribe = $subscribe->customerSubscribe($this->arParams['DATA']['EMAIL'], $userId);
            \CSubscription::Authorize($id);
            $result['status'] = 'success';
            $result['msg'] = 'Подписка на рассылку успешно добавлена';
        } else {
            $result['msg'] = $subscribe->LAST_ERROR;
        }
        return $result;
    }

    /**
     * Инициализация шаблона ajax
     */
    protected function executeAjaxComponent()
    {
        $result = $this->addSubscribe();
        die(json_encode($result));
    }
    
    /**
     * Выполяет действия перед кешированием 
     */
    protected function executeProlog()
    {
        if($this->arParams['AJAX'] == 'Y') {
            return $this->executeAjaxComponent();
        }
        return false;
    }
    
    /**
     * Выполняет действия после выполения компонента
     */
    protected function executeEpilog()
    {
        return false;
    }

    /**
     * Инициализация компонента
     */
    public function executeComponent()
    {
        try
        {
            $this->checkModules();
            $this->checkParams();
            $this->executeProlog();
            $this->checkJS();
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

?>
