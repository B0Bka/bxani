<?
namespace Aniart\Main\Tools\FormValidation\Validation;

use Aniart\Main\Tools\FormValidation\AbstractValidation;

class ChangePassword extends AbstractValidation
{
    protected $minPassLength = 6;
    protected $requiredFields = ['LOGIN', 'PASSWORD', 'CONFIRM_PASSWORD', 'CHECKWORD'];

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

        if(count($arRequired) > 0 && count($arError) > 0) $arError = array_merge($arRequired, $arError);
        elseif(count($arRequired) > 0) $arError = $arRequired;

        return (count($arError) > 0) ? $arError : false;
    }

    public function checkPass($pass)
    {
        return (strlen($pass) >= $this->minPassLength);
    }
}

