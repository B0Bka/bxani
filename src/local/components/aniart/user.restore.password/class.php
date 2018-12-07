<?php

use Aniart\Main\Exceptions\AniartException,
	Aniart\Main\Tools\ForgotPassword;

class AniartRestorePasswordComponent extends CBitrixComponent
{
    public function __construct($component = null)
    {
        parent::__construct($component);
    }
    protected function checkParams()
    {
        $result = [
            'AJAX_MOD' => isset($arParams['AJAX_MOD']) ? $arParams['AJAX_MOD'] : '',
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
			if(isset($_REQUEST["email"]) && isset($_REQUEST["code"]))
			{
				$email = htmlspecialcharsEx(urldecode($_REQUEST["email"]));
				$code = htmlspecialcharsEx($_REQUEST["code"]);
				$this->arResult = ForgotPassword::checkCode($code, $email);
			}
			$this->IncludeComponentTemplate();
		}
		catch (Exception $e)
        {
            $this->abortDataCache();
            ShowError($e->getMessage());
        }
    }
}