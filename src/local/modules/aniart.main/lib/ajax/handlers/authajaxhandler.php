<?php

namespace Aniart\Main\Ajax\Handlers;
use Aniart\Main\Ajax\AbstractAjaxHandler;
use Aniart\Main\Tools\FormValidation;
use Aniart\Main\Tools\ForgotPassword;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule('subscribe');

Loc::loadMessages(__FILE__);

class AuthAjaxHandler extends AbstractAjaxHandler
{

    protected function getFunction()
    {
        return $this->request['func'];
    }

    protected function getLogin()
    {
        global $USER;
        global $APPLICATION;
        if(!is_object($USER))
        {
            $USER = new \CUser;
        }
        $data = $this->post['form'];
        $validation = new FormValidation($data, 'auth');
        $validationResult = $validation->checkValidation();
        if($validationResult)
            return $this->setError($validationResult);
        $authResult = $USER->Login($data['LOGIN'], $data['PASSWORD'], 'Y');
        $APPLICATION->arAuthResult = $authResult;
        if($authResult['TYPE'] == 'ERROR')
        {
            return $this->setError($authResult);
        }
        return $this->setOK($authResult);
    }



    protected function getRegister()
    {
        global $USER;
        global $APPLICATION;
        if(!is_object($USER))
        {
            $USER = new \CUser;
        }
        $data = $this->post['form'];
        $validation = new FormValidation($data, 'register');
        $validationResult = $validation->checkValidation();
        if($validationResult)
            return $this->setError($validationResult);

        //проверяем был ли пользователь импортирован с 1с и не был авторизирован. Если да, то меняем логин, емейл и авторизируем
        $phone = _normalizePhone($data['PHONE'], '38');
        $existUser = \Aniart\Main\Ext\User::ExistsByPhone($phone);
        $userBitrix = reset($existUser);

        if($existUser && empty($userBitrix['LAST_LOGIN']))
        {
            echo($userBitrix['LAST_LOGIN']);
            $userId = $userBitrix['ID'];
            $authResult = $USER->Authorize($userId);

            $userFields =$this->normalizeData($data);
            $this->updateUser($userFields, $userBitrix);
            if($authResult) return $this->setOK($authResult);
                else return $this->setError($authResult);
        }
        elseif($existUser && !empty($userBitrix['LAST_LOGIN']))
        {
            return $this->setError(['PHONE' => i18n('USER_EXIST', 'auth')]);
        }

        $result = $USER->Register(
            $data['EMAIL'],
            $data['NAME'],
            $data['LAST_NAME'],
            $data['PASSWORD'],
            $data['CONFIRM_PASSWORD'],
            $data['EMAIL']
        );

        //save dop params!

        if($result['TYPE'] == 'ERROR')
        {
            return $this->setError($arError);
        }
        $userId = $USER->GetID();

        //update user params..
        $update = $USER->update(
            $userId,
            [
                'PERSONAL_CITY'=>$data['CITY'],
                'PERSONAL_PHONE'=>$data['PHONE'],
                'PERSONAL_STREET'=>$data['STREET'],
                'UF_HOUSE'=>$data['HOUSE'],
                'UF_FLAT'=>$data['FLAT']
            ]
        );
        if($update)
        {
            $result['UPDATE'] = $update;
        }
        else
        {
            $result['UPDATE'] = $update->LAST_ERROR;
        }
        //..update user params

        //add to retailCrm
        $retailCrm = new \Aniart\Main\Tools\retailCrmHelper;
        $newCustomer = $retailCrm->customerAdd($userId, $data);

        //add subscribe..
        if($data['SUB'] != 'on')
        {
            return $this->setOK($result);
        }
        $subscribe = new \CSubscription;
        $addSubscribe = $subscribe->Add([
            'USER_ID' => ($USER->IsAuthorized() ? $userId : false),
            'FORMAT' => 'html',
            'EMAIL' => $data['EMAIL'],
            'ACTIVE' => 'Y',
            'RUB_ID' => [1],
            'SEND_CONFIRM' => 'Y'
        ]);
        if(!empty($addSubscribe))
        {
            $result['SUB'] = \CSubscription::Authorize($addSubscribe);
        }
        else
        {
            $result['SUB'] = $subscribe->LAST_ERROR;
        }
        //..add subscribe

        return $this->setOK($result);
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