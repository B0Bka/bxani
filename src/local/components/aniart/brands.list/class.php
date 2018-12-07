<?php

use Aniart\Main\Exceptions\AniartException,
    Aniart\Main\Repositories\HLBlockRepository,
    Bitrix\Main\Security\Sign\Signer;

class CBrandsListComponent extends CBitrixComponent
{

    protected $brandsRepository;

    public function __construct($component = null)
    {
        $this->brandsRepository = new HLBlockRepository(HL_BRANDS_ID);
        parent::__construct($component);
    }
    
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE']?:'A';
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME']?:36000;

        return parent::onPrepareComponentParams($arParams);
    }
    
    public function executeComponent()
    {
        try
        {
            $this->doExecuteComponent();
        }
        catch(AniartException $e)
        {
            ShowError($e->getMessage());
        }
    }

    private function doExecuteComponent()
    {
        if($this->StartResultCache())
        {
        	$this->init();
	        $this->IncludeComponentTemplate();
        }
    }
    
    private function init()
    {   
        //dBug($this->citiesRepository);
        $this->arResult['BRANDS'] = $this->brands();
    }
    
    private function brands()
    {
        return $this->brandsRepository->getList(
            $this->getSort(), 
            $this->getFilter()
        );
    }
    
    private function getSort()
    {
        return [
            'ID' => 'ASC'
        ];
    }
    
    private function getFilter()
    {
        return ["UF_MAIN"=>1];
    }

    public function getSignedComponentParams()
	{
		$signer = new Signer();
		return $signer->sign(base64_encode(serialize($this->arParams)), 'brands.list');
	}
}

?>
