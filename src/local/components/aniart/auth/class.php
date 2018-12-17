<?php
use Aniart\Main\Models\Product,
    Aniart\Main\FavoritesTable;

class AuthComponent extends CBitrixComponent
{
    protected $request;

    public function __construct($component = null)
    {
        parent::__construct($component);
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
        $this->arResult['AUTH_SERVICES'] = $this->getSocServices();
        $this->IncludeComponentTemplate();
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