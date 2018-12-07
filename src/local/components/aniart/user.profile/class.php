<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Localization\Loc as Loc,
    Aniart\Main\Tools\FormValidation,
    Aniart\Main\Tools\ForgotPassword;
class UserProfile extends CBitrixComponent
{
    protected $cacheKeys = [];
    protected $cacheAddon = [];
    
    protected $post;
    protected $get;
    protected $request;
    
    protected $userData;

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
        
        if($this->arParams['AJAX_MOD'] == 'Y') {
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
        
        return $this->arResult = $this->getUserResult();
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
        $this->userData = $this->getUserData();
    }
    
    protected function getUserResult()
    {
        $result = $this->userData;
        $result['PERSONAL_BIRTHDAY'] = $this->getBirthdayDate();

        if(isset( $this->get["email"]) && isset( $this->get["code"]))
        {
            $email = htmlspecialcharsEx(urldecode( $this->get["email"]));
            $code = htmlspecialcharsEx( $this->get["code"]);
            $result['PASSWORD_RECOVERY'] = ForgotPassword::checkCode($code, $email);
        }

        return $result;
    }

    protected function getBirthdayDate()
    {
        $data = $this->userData['PERSONAL_BIRTHDAY'];
        if(empty($data))
        {
            return false;
        }
        $date = \DateTime::createFromFormat('d.m.Y', $data);
        return date_format($date, 'Y-m-d');
        
    }

    protected function getUserData()
    {
        $id = $this->getUserId();
        if(empty($id))
        {
            return false;
        }
        $user = CUser::GetByID($id);
        return $user->Fetch();
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
    
    protected function ajaxSave()
    {
        global $USER;
        if(!is_object($USER))
        {
            $USER = new \CUser;
        }
        $data = $this->post['form'];
        $id = IntVal($USER->GetID());
        $dateBirthday = \DateTime::createFromFormat('Y-m-d', $data['UF_BIRTHDAY']);
        $married = ($data['UF_MARRIED']=='on'?'1':'');
        $children = [];
        foreach($data as $i=>$item)
        {
            if(stristr($i, 'UF_CHILD_') !== FALSE)
            {
                $children[] = $item;
            }
        }

        $arData = [
            'EMAIL'=>$data['EMAIL'],
            'NAME'=>$data['NAME'],
            'LAST_NAME'=>$data['LAST_NAME'],
            'PERSONAL_PHONE'=>$data['PHONE'],
            'PERSONAL_CITY'=>$data['PERSONAL_CITY'],
            'PERSONAL_STREET'=>$data['PERSONAL_STREET'],
            'PERSONAL_BIRTHDAY'=>date_format($dateBirthday, 'd.m.Y'),
            'UF_HOUSE'=>$data['UF_HOUSE'],
            'UF_FLAT'=>$data['UF_FLAT'],
            'UF_MARRIED'=>$married,
            'UF_CHILDREN'=>$children
        ];
        if(!empty($data['PASSWORD']))
        {
            $arData['PASSWORD'] = $data['PASSWORD'];
            $arData['CONFIRM_PASSWORD'] = $data['CONFIRM_PASSWORD'];
        }

        $validation = new FormValidation($data, 'profile');
        $validationResult = $validation->checkValidation();
        if($validationResult) return $this->setError($validationResult);
        else
        {
            $result = $USER->update($id, $arData);
            if($result)
            {
                return $this->setOK(i18n('DATA_SAVED'));
            }
        }
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

?>