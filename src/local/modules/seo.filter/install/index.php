<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
if (class_exists('seo_filter')) {
    return;
}
class seo_filter extends CModule
{
    public $MODULE_ID = 'seo.filter';
    public $MODULE_VERSION = '1.0';
    public $MODULE_VERSION_DATE = '2019-01-01';
    public $MODULE_NAME = 'СЕО для фильтра';
    public $MODULE_DESCRIPTION = 'Установка мета тегов для каталога';
    public $MODULE_GROUP_RIGHTS = 'N';
    public $PARTNER_NAME = "Custom";

    public function DoInstall()
    {
        global $APPLICATION;
        RegisterModule($this->MODULE_ID);
    }
    public function DoUninstall()
    {
        global $APPLICATION;
        UnRegisterModule($this->MODULE_ID);
    }
}
?>