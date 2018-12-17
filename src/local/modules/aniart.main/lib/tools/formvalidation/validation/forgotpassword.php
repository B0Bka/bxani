<?
namespace Aniart\Main\Tools\FormValidation\Validation;

use Aniart\Main\Tools\FormValidation\AbstractValidation;

class ForgotPassword extends AbstractValidation
{
    protected $requiredFields = ['EMAIL'];

    public function validate()
    {
        $arRequired = $this->getRequired();
        if(!$this->checkEmail($this->data['EMAIL']) && strlen($this->data['EMAIL']) > 0)
            $arError['EMAIL'] = i18n('ERROR_EMAIL', 'register');

        if(count($arRequired) > 0 && count($arError) > 0)
            $arError = array_merge($arRequired, $arError);
        elseif(count($arRequired) > 0)
            $arError = $arRequired;

        return (count($arError) > 0) ? $arError : false;
    }
}

