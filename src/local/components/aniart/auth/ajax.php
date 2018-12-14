<?php

namespace Aniart\Main\Ajax\Handlers;

use Aniart\Main\Ajax\AbstractAjaxHandler,
    Aniart\Main\Tools\FormValidation\Validation\Auth;

class UserAuthAjaxHandler extends AbstractAjaxHandler
{

    protected function getLogin()
    {
        global $USER;
        global $APPLICATION;
        if(!is_object($USER))
        {
            $USER = new \CUser;
        }
        $data = $this->post['form'];
        $fields = $this->normalizeData($data);
        $validation = new Auth($fields, 'auth');
        $validationResult = $validation->validate();
        if($validationResult)
            return $this->setError($validationResult);

        $authResult = $USER->Login($fields['LOGIN'], $fields['PASSWORD'], 'Y');
        $APPLICATION->arAuthResult = $authResult;
        if($authResult['TYPE'] == 'ERROR')
        {
            return $this->setError($authResult);
        }
        return $this->setOK($authResult);
    }

    private function normalizeData($data)
    {
        $result['LOGIN'] = $data['LOGIN'];
        $result['PASSWORD'] = $data['PASSWORD'];
        return $result;
    }
}