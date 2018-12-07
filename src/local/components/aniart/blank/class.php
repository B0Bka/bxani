<?php

class AniartBlankComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        global $USER;
        if(!is_numeric($arParams['CACHE_TIME'])){
            $arParams['CACHE_TIME'] = 3600;
        }
        if(empty($arParams['CACHE_KEY'])){
            $usersGroups = '';
            if(is_object($USER) && $USER->IsAuthorized()){
                $usersGroups = $USER->GetGroups();
            }
            $arParams['CACHE_KEY'] = $usersGroups;
        }
        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->StartResultCache()) {
            $this->IncludeComponentTemplate();
        }
    }

} 