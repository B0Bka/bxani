<?php

namespace Aniart\Main\Ajax\Handlers;

use Aniart\Main\Ajax\AbstractAjaxHandler,
    Aniart\Main\Tools\FormValidation\Validation\ForgotPassword,
    Bitrix\Main\Mail\Event;

class RestorePasswordAjaxHandler extends AbstractAjaxHandler
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getRestore()
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

        $validation = new ForgotPassword($fields, 'register');
        $validationResult = $validation->validate();
        if($validationResult)
            return $this->setError($validationResult);

        $arResult = $USER->SendPassword($fields['EMAIL'], $fields['EMAIL']);
        if ($arResult["TYPE"] == "OK")
        {
            return $this->setOK(i18n('RESTORE_SUCCESS', 'restore'));
        }
        else
            return $this->setError(['EMAIL' => i18n('RESTORE_ERROR', 'restore')]);
    }

    private function normalizeData($data)
    {
        $result['EMAIL'] = $data['EMAIL'];
        return $result;
    }
}