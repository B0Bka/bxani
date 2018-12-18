<?php

class AniartRestorePasswordComponent extends CBitrixComponent
{
    public function __construct($component = null)
    {
        parent::__construct($component);
    }
    protected function checkParams()
    {
        $result = [
            'CACHE_TYPE' => isset($arParams['CACHE_TYPE']) ? $arParams['CACHE_TYPE'] : 'N',
            'CACHE_TIME' => isset($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : 36000000,
        ];
        return $result;
    }
    public function executeComponent()
    {
		try
        {
			$this->checkParams();
			$this->IncludeComponentTemplate();
		}
		catch (Exception $e)
        {
            $this->abortDataCache();
            ShowError($e->getMessage());
        }
    }
}