<?php
class AniartPromotionsListComponent extends CBitrixComponent
{
    protected $promotionsRepository;

    public function __construct($component = null)
    {
        parent::__construct($component);

        $this->promotionsRepository = app('PromotionsRepository');
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE'] ?: 'A';
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ?: 36000;
        $arParams['FILTER'] = array_merge((array)$arParams['FILTER'], [
            'ACTIVE' => 'Y'
        ]);
        $arParams['SORT'] = $arParams['SORT'] ?: ['DATE_CREATED' => 'DESC'];
	    $arParams['PAGE_VAR'] = $arParams['PAGE_VAR'] ?: 'page';
        $arParams['PAGE_SIZE'] = $arParams['PAGE_SIZE'] ?: 20;
        $arParams['PAGE_NUM'] = $this->request->get($arParams['PAGE_VAR']) ?: 1;
        $arParams['SORT_VAR'] = $arParams['SORT_VAR'] ?: 'sort';

        return parent::onPrepareComponentParams($arParams);
    }

    public function executeComponent()
    {
        try
        {
            $this->doExecuteComponent();
        }
        catch(\Aniart\Main\Exceptions\AniartException $e)
        {
            ShowError($e->getMessage());
        }
    }

    private function doExecuteComponent()
    {
        if($this->StartResultCache())
        {
        	$this->initItems();
	        $this->IncludeComponentTemplate();
        }
    }

    private function initItems()
    {
        $items = $this->promotionsRepository->getList(
            $this->arParams['SORT'],
            ['ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y'],
            false, 
            [
                'iNumPage' => $this->arParams['PAGE_NUM'],
                'bShowAll' => false,
                'nPageSize' => $this->arParams['PAGE_SIZE']
            ]
        );
        $this->arResult['ITEMS'] = array_values($items);
		$this->arResult['SECTION_ID'] =  $this->arParams['FILTER']['SECTION_ID'];
        if($dbResult = $this->promotionsRepository->getLastDBResult())
        {
	        $this->arResult['DB_RESULT'] = $dbResult;
	        $this->arResult['PAGINATION'] = [
	        	'NavNum' => $dbResult->NavNum, //порядковый номер постранички на странице
		        'NavPageCount' => $dbResult->NavPageCount, //количество страниц
		        'NavPageNomer' => $dbResult->NavPageNomer, //номер текущей страницы
		        'NavPageSize' => $dbResult->NavPageSize, //количество элементов на странице
		        'NavRecordCount' => $dbResult->NavRecordCount, //количество элементов в базе,
		        'NavPageVar' => $this->arParams['PAGE_VAR']
	        ];
        }
    }
}