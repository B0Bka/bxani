<?php

namespace Aniart\Main\Ajax\Handlers;
use Aniart\Main\Ajax\AbstractAjaxHandler;
use Aniart\Main\Tools\FormValidation;
use Aniart\Main\Tools\ForgotPassword;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class AuthAjaxHandler extends AbstractAjaxHandler
{

    protected function getFunction()
    {
        return $this->request['func'];
    }



    protected function getForgot()
    {
        $data = $this->post['form'];
        $validation = new FormValidation($data, 'forgot');
        $validationResult = $validation->checkValidation();
        if($validationResult)
            return $this->setError($validationResult);
        $res = ForgotPassword::sendForgotEmail($data['EMAIL']);
        if($res) $this->setOK(['message' => $res]);
        else $this->setError($res);
    }

    protected function getLogout()
    {
        global $USER;
        if(!is_object($USER))
        {
            $USER = new \CUser;
        }
        $result = $USER->Logout();
        return $this->setOK($result);
    }

    public function getSocIcons()
    {
        $str = '';
        $sAuth = new \Aniart\Main\Services\SocAuthList();
        $arSocLinks = $sAuth->getList();
        foreach($arSocLinks as $service => $link)
            $str .= '<a class="'.$service.'" href="javascript:void(0)"  onclick="BX.util.popup(\''.$link['URL'].'\', 630, 450)">
                '.$link['SVG'].'
            </a>';
        $this->setOK($str);
    }

    private function normalizeData($data)
    {
        $result = [
            'LOGIN' => $data['EMAIL'],
            'NAME' => $data['NAME'],
            'LAST_NAME' => $data['LAST_NAME'],
            'PASSWORD' => $data['PASSWORD'],
            'CONFIRM_PASSWORD' => $data['CONFIRM_PASSWORD'],
            'EMAIL' => $data['EMAIL'],
            'PERSONAL_CITY' => $data['CITY'],
            'PERSONAL_PHONE' => _normalizePhone($data['PHONE']),
            'PERSONAL_STREET' => $data['STREET'],
            'UF_HOUSE' => $data['HOUSE'],
            'UF_FLAT' => $data['FLAT']
        ];
        return $result;
    }

    private function updateUser($fields, $bitrixFields)
    {
        foreach($fields as $key => $field)
        {
            if($bitrixFields[$key] != $field && !empty($field)) $arUpdate[$key] = $field;
        }
        if(!empty($arUpdate))
        {
            $updUser = new \CUser;
            $updUser->Update($bitrixFields['ID'], $arUpdate);
            if($updUser->LAST_ERROR) return false;
                else return true;
        }
    }

}