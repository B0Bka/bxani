<?php

namespace Aniart\Main\Ajax\Handlers;


use Aniart\Main\Ajax\AbstractAjaxHandler;
use Aniart\Main\Models\NpDepartment;
use Bitrix\Main\Context;
use Bitrix\Main\EventManager;
use Bitrix\Main\Security\Sign\BadSignatureException;
use Aniart\Main\Repositories\NpCitiesRepository;
use Aniart\Main\Repositories\NpDepartmentsRepository;
use Aniart\Main\Models\NpCity;

class OrderAjaxHandler extends AbstractAjaxHandler
{

    protected $newPostCities;
    protected $newPostDepartments;
    protected $cityRepository;
    protected $shopsRepository;
    public function __construct()
    {
        parent::__construct();
        $this->newPostDepartments = app('NpDepartmentsRepository');
        $this->newPostCities = app('NpCitiesRepository');
        $this->cityRepository = app('CitiesRepository');
        $this->shopsRepository = app('ShopsRepository');

    }

    public function authorize()
    {
        try{
            $params = $this->getComponentParamsFromRequest();
        }
        catch (BadSignatureException $e)
        {
            return $this->setError($e->getMessage());
        }

        EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', function(&$content) use (&$html){
	        if($json = json_decode($content)){
	            $content = $json;
            }
            global $USER;
	        $content = json_encode(['status' => 'ok', 'auth' => $USER->IsAuthorized(), 'data' => ['html' => $content]]);
        });

        global $APPLICATION;
        $request = Context::getCurrent()->getRequest();
        $request->modifyByQueryString('&via_ajax=Y');
        //TODO компонент формирует ошибки только на русском языке(согласно языковой системы Битрикса), нужно как-то эту ситуацию обработать
        $APPLICATION->IncludeComponent('aniart:sale.order', 'new', $params);
    }

	public function processOrder()
    {
        \Bitrix\Main\Diag\Debug::writeToFile($_REQUEST,"request","/local/logs/orderdebug.txt");
        \Bitrix\Main\Diag\Debug::writeToFile($_SERVER['HTTP_USER_AGENT'],"browser","/local/logs/orderdebug.txt");
        try{
            $params = $this->getComponentParamsFromRequest();
        }
        catch (BadSignatureException $e)
        {
            return $this->setError($e->getMessage());
        }

	    EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', function(&$content) use (&$html){
	        if($json = json_decode($content)){
	            $content = $json;
            }
	        $content = json_encode(['status' => 'ok', 'data' => ['html' => $content]]);
	    });
        \Bitrix\Main\Diag\Debug::writeToFile('ok',"ok","/local/logs/orderdebug.txt");
		global $APPLICATION;
        $APPLICATION->IncludeComponent('aniart:sale.order', 'new', $params);
    }

	protected function getComponentParamsFromRequest($salt = 'sale.order', $requestKey = 'signedParamsString')
	{
		return parent::getComponentParamsFromRequest($salt, $requestKey);
	}

   public function oneClickBuy()
   {
       try{
            $params = $this->getComponentParamsFromRequest();
        }
        catch (BadSignatureException $e)
        {
            return $this->setError($e->getMessage());
        }

	    EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', function(&$content) use (&$html){
	        if($json = json_decode($content)){
	            $content = $json;
            }
	        $content = json_encode(['status' => 'ok', 'data' => ['html' => $content]]);
	    });
		global $APPLICATION;
        $APPLICATION->IncludeComponent('aniart:sale.order', 'new', $params);
   }

	public function getNewPostCities()
    {
        $query = trim($this->post['query']);
        if(strlen($query) < 2){
            return $this->setError(i18n('NP_CITIES_INVALID_QUERY', 'order'));
        }
        try{
            $cities = $this->newPostCities->getCitiesByQuery($query);
            $data = array_map(function(NpCity $city){
                $cityData = $city->toArray();
                $cityData['NAME'] = $city->getName($this->post['lang']);
                return $cityData;
            }, $cities);

            return $this->setOK(array_values($data));
        }
        catch (NewPostServiceException $e){
            return $this->setError($e->getMessage());
        }
   }

   public function getNewPostDepartmentsByCityRef()
   {
       $cityRef = trim($this->post['cityRef']);
       if(!$cityRef){
           return $this->setError(i18n('NP_DEPARTMENTS_INVALID_CITY', 'order'));
       }
        try{
           $showStores = false;
           $name = $this->newPostCities->getCityNameByRef($cityRef);
           $city = reset($this->cityRepository->getByName($name, $this->post['lang']));
           if(!empty($city) && $this->post['payment'] == 3)
           {
               $cityId = $city->getId();
               $arFilter = ["!UF_CHAIN" => false, 'UF_LOCATION_CITY' => $cityId];
               $shops = $this->shopsRepository->getList([], $arFilter);
               if(!empty($shops)) $showStores = true;
           }
           $departments = $this->newPostDepartments->getDepartmentsByCityRef($cityRef);
           $data = array_map(function (NpDepartment $department) {
                $depData = $department->toArray();
                $depData['NAME'] = $department->getName($this->post['lang']);
                $depData['CITY_NAME'] = $this->newPostCities->getCityNameByRef($depData['UF_CITY_REF_ID'], $this->post['lang']);
                return $depData;
            }, $departments);
           return $this->setOK(['DEPARTMENTS' => array_values($data), 'SHOW_STORES_DELIVERY' => $showStores]);
        }

       catch(NewPostServiceException $e){
           return $this->setError($e->getMessage());
       }
   }
}