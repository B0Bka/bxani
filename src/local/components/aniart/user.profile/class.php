<?php
use Aniart\Main\Models\Product,
    Aniart\Main\FavoritesTable;

class UserProfile extends CBitrixComponent
{
    private $sort = [
        "EMAIL" => ['SORT' => 60, 'TYPE' => 'email'],
        "NAME" => ['SORT' => 10, 'TYPE' => 'text'],
        "LAST_NAME" => ['SORT' => 20, 'TYPE' => 'text'],
        "PERSONAL_BIRTHDAY" => ['SORT' => 140, 'TYPE' => 'date'],
        "PERSONAL_PHONE" => ['SORT' => 30, 'TYPE' => 'text', 'CLASS' => 'phone'],
        "PERSONAL_MOBILE" => ['SORT' => 40, 'TYPE' => 'text', 'CLASS' => 'phone'],
        "UF_WHATSAPP" => ['SORT' => 50, 'TYPE' => 'text', 'CLASS' => 'phone whatsapp-input'],
        "PERSONAL_CITY" => ['SORT' => 70, 'TYPE' => 'text'],
        "UF_TYPE" => ['SORT' => 80, 'TYPE' => 'list'],
        "WORK_COMPANY"=>['SORT' => 90, 'TYPE' => 'text'],
        "WORK_POSITION"=>['SORT' => 100, 'TYPE' => 'text'],
        "UF_VOEN" => ['SORT' => 110, 'TYPE' => 'text'],
        "PASSWORD" => ['SORT' => 120, 'TYPE' => 'password'],
        "CONFIRM_PASSWORD" => ['SORT' => 130, 'TYPE' => 'password'],
    ];
    protected $request;

    public function __construct($component = null)
    {
        parent::__construct($component);


    }

    public function executeComponent()
    {
        try {
            $this->doExecuteComponent();
        } catch (AniartException $e) {
            ShowError($e->getMessage());
        }
    }

    private function doExecuteComponent()
    {
        $this->initParams();
        $this->getFields();
        if($this->arParams['SOC_AUTH'] == 'Y')
            $this->arResult['AUTH_SERVICES'] = $this->getSocServices();

        $this->IncludeComponentTemplate();
    }

    private function getFields()
    {
        foreach ($this->arParams['SHOW_FIELDS'] as $field)
        {
            $fields[] = $this->makeField($field);
        }
        if(!empty($this->arParams["USER_PROPERTY"]))
        {
            foreach ($this->arParams['USER_PROPERTY'] as $field)
            {
                $fields[] = $this->makeField($field);
            }
        }
        $this->sortFields($fields);
    }

    private function makeField($field)
    {
        return [
            'CODE' => $field,
            'REQUIRED' => $this->isRequired($field),
            'TYPE' => $this->getParam($field, 'TYPE', 'text'),
            'ADDITIONAL_CLASS' => $this->getParam($field, 'CLASS', false),
            'LIST' => $this->getParam($field, 'LIST', ''),
        ];
    }

    private function isRequired($code)
    {
        return in_array($code, $this->arParams["REQUIRED_FIELDS"]);
    }

    private function sortFields($fields)
    {
        if(empty($fields))
            return false;

        foreach($fields as $field)
        {
            $key = $this->getParam($field['CODE'], 'SORT', 1000);
            $this->arResult['FIELDS'][$key] = $field;
        }
        ksort($this->arResult['FIELDS']);
    }

    private function getParam($key, $code, $default = '')
    {
        if(!empty($this->sort[$key][$code]))
            return $this->sort[$key][$code];
        else return $default;
    }

    private function initParams()
    {
        foreach($this->sort as $key => $field)
        {
            if($field['TYPE'] == 'list')
            {
                $this->sort[$key]['LIST'] = $this->getEnum($key);
            }
        }
    }

    private function getEnum($code)
    {
        $rsData = \CUserTypeEntity::GetList( array(), array('ENTITY_ID' => 'USER', 'FIELD_NAME' => $code) );
        while($arRes = $rsData->Fetch())
            $id = $arRes["ID"];

        $rsList = \CUserFieldEnum::GetList(array('SORT' => 'asc'), array(
            "USER_FIELD_ID" => $id,
        ));
        while($arListRes = $rsList->GetNext())
            $result[$arListRes['ID']] = $arListRes["VALUE"];

        return $result;
    }

    private function getSocServices()
    {
        if(!\CModule::IncludeModule("socialservices"))
            return false;

        $oAuthManager = new \CSocServAuthManager();
        return $oAuthManager->GetActiveAuthServices($this->arResult);
    }

    public function getSignedComponentParams()
    {
        $signer = new \Bitrix\Main\Security\Sign\Signer();
        return $signer->sign(base64_encode(serialize($this->arParams)), 'register_'.$this->arParams['TYPE']);
    }
}