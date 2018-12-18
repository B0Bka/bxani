<?php

namespace Aniart\Main\Ajax\Handlers;

use Aniart\Main\Ajax\AbstractAjaxHandler,
    Aniart\Main\Tools\FormValidation\Validation\ChangePassword;

class ChangePasswordAjaxHandler extends AbstractAjaxHandler
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getChange()
    {
        global $USER;
        if(!is_object($USER))
        {
            $USER = new \CUser;
        }
        $data = $this->post['form'];
        $fields = $this->normalizeData($data);
        if(empty($fields))
            return $this->setError('empty fields');

        $validation = new ChangePassword($fields, 'register');
        $validationResult = $validation->validate();
        if($validationResult)
            return $this->setError($validationResult);

        $arResult = $USER->ChangePassword($fields['LOGIN'], $fields['CHECKWORD'], $fields['PASSWORD'], $fields['CONFIRM_PASSWORD']);

        if ($arResult["TYPE"] == "OK")
            return $this->setOK(i18n('CHANGE_SUCCESS', 'change'));
        else
            return $this->setError(['CONFIRM_PASSWORD' => strip_tags($arResult["MESSAGE"])]);
    }

    private function normalizeData($data)
    {
        $result['LOGIN'] = $data['LOGIN'];
        $result['CHECKWORD'] = $data['CHECKWORD'];
        $result['PASSWORD'] = $data['PASSWORD'];
        $result['CONFIRM_PASSWORD'] = $data['CONFIRM_PASSWORD'];
        return $result;
    }
}