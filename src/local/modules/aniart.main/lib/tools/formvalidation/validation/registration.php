<?
namespace Aniart\Main\Tools\FormValidation\Validation;

use Aniart\Main\Tools\FormValidation\AbstractValidation;
use Aniart\Main\Ext\User;

class Registration extends AbstractValidation
{
    protected $minPassLength = 6;
    protected $requiredFields = [];

    public function setRequiredFields($fields)
    {
        $this->requiredFields = $fields;
    }

    public function validate()
    {
        $arError = [];
        $arRequired = $this->getRequired();

        if($this->data['PASSWORD'] != $this->data['CONFIRM_PASSWORD'] && strlen($this->data['CONFIRM_PASSWORD']) > 0)
            $arError['CONFIRM_PASSWORD'] = i18n('ERROR_DIFFRENT_PASSWORD', 'register');
        if(strlen($this->data['PASSWORD']) > 0 && !$this->checkPass($this->data['PASSWORD']))
            $arError['PASSWORD'] = i18n('ERROR_SHORT_PASSWORD', 'register');
        if(!$this->checkEmail($this->data['EMAIL']) && strlen($this->data['EMAIL']) > 0)
            $arError['EMAIL'] = i18n('ERROR_EMAIL', 'register');
        if(!empty(User::ExistsByEmail($this->data['EMAIL'])) && strlen($this->data['EMAIL']) > 0) {
            $usr = array_shift(User::ExistsByEmail($this->data['EMAIL']));
            $arError['EMAIL'] = i18n('EMAIL_EXISTS', 'register') ;
        }
        //if(!checkPhone($this->data['PHONE']) && strlen($this->data['PHONE']) > 0) $arError['PHONE'] = i18n('ERROR_PHONE', 'register');

        if(count($arRequired) > 0 && count($arError) > 0) $arError = array_merge($arRequired, $arError);
        elseif(count($arRequired) > 0) $arError = $arRequired;

        return (count($arError) > 0) ? $arError : false;
    }

    public function checkPass($pass)
    {
        return (strlen($pass) >= $this->minPassLength);
    }
}

