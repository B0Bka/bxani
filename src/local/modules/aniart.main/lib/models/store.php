<?php

namespace Aniart\Main\Models;

class Store extends AbstractModel
{

    public function getFullName($lang = 'ru')
    {
        return $this->getAddress($lang);
    }

    public function getTitle($lang = 'ru')
    {
        return $this->fields['~TITLE'];
    }

    public function getAddress($lang = null)
    {
        $lang = $lang ?: i18n()->lang();
        return i18n()->isLangDefault($lang) ? $this->fields['~ADDRESS'] : $this->fields['~UF_ADDRESS_'.strtoupper($lang)];
    }

    public function isIssuingCenter()
    {
        return $this->fields['ISSUING_CENTER'] == 'Y';
    }

    public function getSchedule()
    {
        return $this->fields['SCHEDULE'];
    }
    
    public function getShop()
    {
        return $this->fields['UF_SHOP'];
    }

    public function getCity()
    {
        return $this->fields['UF_CITY'];
    }

}
