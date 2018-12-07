<?php

namespace Aniart\Main\Models;

use Aniart\Main\Interfaces\PricebleInterface;
use Aniart\Main\Repositories\StoresRepository;

class SaleDelivery extends AbstractModel implements PricebleInterface
{

    protected $stores;
    protected $shops;

    /**
     * @var StoresRepository
     */
    protected $storesRepository;
    protected $shopsRepository;
    protected $cityRepository;

    public function __construct(array $fields)
    {
        $this->storesRepository = app('StoresRepository');
        $this->cityRepository = app('CitiesRepository');
        $this->shopsRepository = app('ShopsRepository');
        parent::__construct($fields);
    }

    public function getName($lang = null)
    {
        $lang = $lang ?: i18n()->lang();
        if(i18n()->isLangDefault($lang))
        {
            $name = $this->fields['NAME'];
        }
        else
        {
            $name = i18n("DELIVERY_".$this->getId(), 'order', $lang);
        }
        return $name;
    }

    public function getDescription($lang = 'ru')
    {
        $lang = $lang ?: i18n()->lang();
        if(i18n()->isLangDefault($lang))
        {
            $desc = $this->fields['DESCRIPTION'];
        }
        else
        {
            $desc = i18n("DELIVERY_".$this->getId().'_DESCRIPTION', 'order', $lang);
        }
        return $desc;
    }

    public function hasStores()
    {
        return count($this->getStoresId()) > 0;
    }

    public function isChecked()
    {
        return (isset($this->fields['CHECKED']) && $this->fields['CHECKED'] == 'Y');
    }

    public function getPrice($format = false)
    {
        $price = $this->fields['PRICE'];
        return $format ? FormatCurrency($price, $this->getCurrency()) : $price;
    }

    public function getCurrency()
    {
        return $this->fields['CURRENCY'];
    }

    /**
     * @return Store[]
     */
    public function getStores()
    {
        if(is_null($this->stores))
        {
            $this->stores = array();
            if($this->hasStores())
            {
                $arFilter = array('ACTIVE' => 'Y', 'ID' => $this->getStoresId());
                $this->stores = $this->storesRepository->getList(
                        array('SORT' => 'ASC'), $arFilter
                );
            }
        }
        return $this->stores;
    }
    
    public function getShops($cityName = '')
    {
        if(is_null($this->shops))
        {
            $this->shops = [];
            if($this->hasStores())
            {
                $arFilter = ["!UF_CHAIN" => false];
                if(!empty($cityName))
                {
                    $cityId = $this->getCityByName($cityName);
                    if($cityId > 0)
                        $arFilter['UF_LOCATION_CITY'] = $cityId;
                    else
                        return false; //не выводить список магазинов, у городах без магазинов
                }
                $this->shops = $this->shopsRepository->getList(
                    ['UF_SORT' => 'ASC'], $arFilter
                );
            }
        }
        return $this->shops;
    }

    public function getStoresId()
    {
        return $this->fields['STORE'];
    }

    public function isCourier()
    {
        return $this->getId() == COURIER_DELIVERY_ID;
    }

    public function isNewPost()
    {
        return $this->getId() == NEW_POST_DELIVERY_ID || $this->getId() == NEW_POST_DELIVERY_PAID_ID;
    }

    public function isNewPostStores()
    {
        return $this->getId() == NEW_POST_STORE_DELIVERY_ID || $this->getId() == NEW_POST_STORE_DELIVERY_PAID_ID;
    }
    
    public function isShopStores()
    {
        return $this->getId() == SHOP_STORE_DELIVERY_ID;
    }

    private function getCityByName($name)
    {
        $city = reset($this->cityRepository->getByName($name));
        return !empty($city) ? $city->getId() : '';
    }

    public function isShopInCity($name)
    {
        if(!empty($this->getShops($name)))
            return true;

        return false;
    }
}
