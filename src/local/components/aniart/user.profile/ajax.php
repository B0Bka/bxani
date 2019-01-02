<?php

namespace Aniart\Main\Ajax\Handlers;

use Aniart\Main\Ajax\AbstractAjaxHandler,
    Aniart\Main\Tools\FormValidation\Validation\Registration,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Mail\Event,
    Bitrix\Main\Security\Sign\BadSignatureException;

class EditProfileAjaxHandler extends AbstractAjaxHandler
{
    private $componentParams;
    public function __construct()
    {
        parent::__construct();
        $this->componentParams = $this->getComponentParamsFromRequest('register_'.$this->post['type'], 'component');
    }

    public function getRegister()
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

        if($this->post["whatsapp"] === "true")
            $this->addRequired('UF_WHATSAPP');

        $validation = new Registration($fields, 'register');
        $validation->setRequiredFields($this->componentParams['REQUIRED_FIELDS']);
        $validationResult = $validation->validate();
        if($validationResult)
            return $this->setError($validationResult);

        $USER = new \CUser;
        $ID = $USER->Add($fields);
        if (intval($ID) > 0)
        {
            $this->sendEmail($fields);
            return $this->setOK('!!!!!!1');
        }
        else
            return $this->setError($USER->LAST_ERROR);
    }

    public function addRequired($code)
    {
        $this->componentParams['REQUIRED_FIELDS'][] = $code;
        return $this->componentParams;
    }


    private function normalizeData($data)
    {
        $paramFields = $this->getAllFields();
        foreach ($paramFields as $field)
        {
            if($field == 'PERSONAL_BIRTHDAY' && !empty($data[$field]))
            {
                $objDateTime = DateTime::createFromPhp(new \DateTime($data[$field]));
                $result[$field] = $objDateTime->toString();
            }
            else
                $result[$field] = $data[$field];
        }

        $result['LOGIN'] = $data['EMAIL'];
        $result['GROUP_ID'] = $this->getGroup();
        return $result;
    }

    private function getGroup()
    {
        if($this->componentParams == 'partner')
        {
            $filter = ["STRING_ID" => "partners"];
            $rsGroups = \CGroup::GetList($by = "c_sort", $order = "asc", $filter); // выбираем группы
            while($arGroups = $rsGroups->Fetch())
            {
                $id = $arGroups['ID'];
            }
        }
        if(!empty($id))
            return array(2, $id);

        return array(2);
    }

    private function sendEmail($fields)
    {
        $res = Event::send(array(
            "EVENT_NAME" => "USER_INFO",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => $fields['EMAIL'],
                "NAME" => $fields['NAME'],
                "LAST_NAME" => $fields['LAST_NAME']
            ),
        ));

        return $res;
    }

    private function getAllFields()
    {
        if(!empty($this->componentParams['USER_PROPERTY']))
            return array_merge($this->componentParams['SHOW_FIELDS'], $this->componentParams['USER_PROPERTY']);

        return $this->componentParams['SHOW_FIELDS'];
    }

    protected function getComponentParamsFromRequest($salt = 'profile', $requestKey = 'signedParamsString')
    {
        return parent::getComponentParamsFromRequest($salt, $requestKey);
    }

}

