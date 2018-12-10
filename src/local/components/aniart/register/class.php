<?php
use Aniart\Main\Models\Product,
    Aniart\Main\FavoritesTable;

class RegisterComponent extends CBitrixComponent
{
    private $sort = [
                "EMAIL" => ['SORT' => 60, 'TYPE' => 'email'],
                "NAME" => ['SORT' => 10, 'TYPE' => 'text'],
                "LAST_NAME" => ['SORT' => 20, 'TYPE' => 'text'],
                "PERSONAL_BIRTHDAY" => ['SORT' => 140, 'TYPE' => 'date'],
                "PERSONAL_PHONE" => ['SORT' => 30, 'TYPE' => 'text', 'CLASS' => 'phone'],
                "PERSONAL_MOBILE" => ['SORT' => 50, 'TYPE' => 'text', 'CLASS' => 'phone'],
                "UF_WHATSAPP" => ['SORT' => 50, 'TYPE' => 'text', 'CLASS' => 'phone'],
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
        $this->initParams();

    }

    public function executeComponent()
    {
        try {
            $this->doExecuteComponent();
        } catch (\Aniart\Main\Exceptions\AniartException $e) {
            ShowError($e->getMessage());
        }
    }

    private function doExecuteComponent()
    {
        $this->getFields();
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
            'TYPE' => $this->getType($field),
            'ADDITIONAL_CLASS' => $this->getClass($field),
            'LIST' => $this->getList($field),
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
            $key = $this->getSort($field['CODE']);
            $this->arResult['FIELDS'][$key] = $field;
        }
        ksort($this->arResult['FIELDS']);
    }

    private function getSort($key)
    {
        if(!empty($this->sort[$key]['SORT']))
            return $this->sort[$key]['SORT'];
        else return 1000;
    }

    private function getType($key)
    {
        if(!empty($this->sort[$key]['TYPE']))
            return $this->sort[$key]['TYPE'];
        else return 'text';
    }

    private function getClass($key)
    {
        if(!empty($this->sort[$key]['CLASS']))
            return $this->sort[$key]['CLASS'];
        else return false;
    }

    private function getList($key)
    {
        if(!empty($this->sort[$key]['LIST']))
            return $this->sort[$key]['LIST'];
        else return false;
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
             $result[] = $arListRes["VALUE"];

        return $result;
    }

    public function getSignedComponentParams()
    {
        $signer = new \Bitrix\Main\Security\Sign\Signer();
        return $signer->sign(base64_encode(serialize($this->arParams)), 'user.favorites');
    }
}