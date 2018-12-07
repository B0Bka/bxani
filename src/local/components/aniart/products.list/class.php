<?php

use Aniart\Main\Models\Product;

class AniartProductsListComponent extends CBitrixComponent
{
    /**
     * @var  \Aniart\Main\Repositories\ProductsRepository;
     */
    protected $productsRepository;
    protected $sectionRepository;
    protected $productsOrmRepository;
	/**
	 * @var \Bitrix\Main\HttpRequest
	 */
    protected $request;

    public function __construct($component = null)
    {
        parent::__construct($component);

        $this->productsRepository = app('ProductsRepository');
        $this->productsOrmRepository = app('ProductsOrmRepository');
        $this->sectionRepository = app('ProductSectionsRepository');
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
    	$this->initSortParams();
        if($this->StartResultCache())
        {
        	$this->initProducts();
        	if($this->arParams['ADD_SECTIONS_CHAIN'] == 'Y' &&!empty($this->arResult['SECTION'])) $this->arResult['SECTION']->setBreadcrumbs();
	        $this->IncludeComponentTemplate();
        }
    }

    private function initSortParams()
    {
    	$sortParams = $this->arParams['SORT_DATA'];
    	$currentSort = $this->request->get($this->arParams['SORT_VAR']);
    	$currentSort = $currentSort ?: 'default';
        foreach($sortParams as $type => &$params)
        {
            if($currentSort == $type)
            {
                $params['active'] = true;
                $this->currentSort = $type;
                $this->arParams['SORT'] = $params['order'];
                break;
            }
        }
        unset($params);
        $this->sortParams = $sortParams;
    }

    public function getSortParams()
    {
        return $this->sortParams;
    }
    
    public function getSortLink($type)
    {
        global $APPLICATION;
        $sort = '';
        if($type !== 'default')
        {
            $sort = $this->currentSort === $type ? '' : 'sort='.$type;
        }
        $link = $APPLICATION->GetCurPageParam($sort, ['sort']);
        return $link;
    }

    /**
     * @return Product[]
     */
    private function initProducts()
    {
        $pageSize = $this->request->get('top') ? $this->arParams['PAGE_SIZE']*$this->request->get('top') : $this->arParams['PAGE_SIZE'];
        if(!empty($this->arParams['SORT']['SORT']) && ($this->isTrendSection() || $this->isSaleSection() || $this->isCatalogSection()) && !$this->isSearchSection())
        {
            $multi = false; //если выбрана фильтрация по множественным полям, то нужно подключить сущность таблицы для множественных полей, иначе будут дубли товаров в списке
            $entityPropsSingle = Bitrix\Main\Entity\Base::compileEntity(
                sprintf('PROPS_SINGLE_%s', PRODUCTS_IBLOCK_ID),
                [
                    'IBLOCK_ELEMENT_ID' => ['data_type' => 'integer'],
	                'PROPERTY_' . PROP_MIN_PRICE_ID => ['data_type' => 'integer'],
                    'PROPERTY_' . PROP_SALE_ID => ['data_type' => 'integer'],
                    'PROPERTY_' . PROP_AVAILABLE_ID => ['data_type' => 'integer'],
                    'PROPERTY_' . PROP_SEASON_ID => ['data_type' => 'integer'],
                    'PROPERTY_' . PROP_NECKLINE_ID => ['data_type' => 'integer'],
                    'PROPERTY_' . PROP_SILUET_ID => ['data_type' => 'integer'],
                    'PROPERTY_' . PROP_LENGTH_ID => ['data_type' => 'integer'],
                    'PROPERTY_' . PROP_SLEEVE_LENGTH_ID => ['data_type' => 'integer'],
                    'PROPERTY_' . PROP_STYLE_ID => ['data_type' => 'integer'],

                ],
                [
                    'table_name' => sprintf('b_iblock_element_prop_s%s', PRODUCTS_IBLOCK_ID),
                ]
            );
            $entityPropsMulti = Bitrix\Main\Entity\Base::compileEntity(
                sprintf('PROPS_MULTI_%i', PRODUCTS_IBLOCK_ID),
                [
                    'IBLOCK_ELEMENT_ID' => ['data_type' => 'integer'],
                    'IBLOCK_PROPERTY_ID' => ['data_type' => 'integer'],
                    'VALUE' => ['data_type' => 'string']
                ],
                [
                    'table_name' => sprintf('b_iblock_element_prop_m%s', PRODUCTS_IBLOCK_ID),
                ]
            );

            foreach ($this->arParams['FILTER'] as $key => $arFilterGroup) {

                $arFilter = [];
                if (is_integer($key)) {
                    $arFilter['LOGIC'] = $arFilterGroup['LOGIC'];
                    foreach ($arFilterGroup as $keyInner => $filterRows) {
                        if (is_integer($keyInner)) {
                            foreach ($filterRows as $keyProp => $filterProp) {
                                $propId = preg_replace('/\D/', '', $keyProp);
                                if ($keyProp == 'PROPERTY_' . PROP_COLOR_ID || $keyProp == 'PROPERTY_' . PROP_SIZE_ID || $keyProp == 'PROPERTY_' . PROP_STYLE_ID) {
                                    $multi[] = $propId;
                                    $arFilter['MULTI'.$propId.'.IBLOCK_PROPERTY_ID'] =  $propId;
                                    $arMultiFilter[$propId][] = ['MULTI'.$propId.'.VALUE' => $filterProp];
                                }
                                elseif($keyProp == '<=PROPERTY_'. PROP_MIN_PRICE_ID || $keyProp == '>=PROPERTY_'. PROP_MIN_PRICE_ID){
	                                if($keyProp == '<=PROPERTY_'. PROP_MIN_PRICE_ID) {
		                                $arFilter[] = ['<=SINGLE.PROPERTY_' . PROP_MIN_PRICE_ID => $filterProp];
	                                }
	                                if($keyProp == '>=PROPERTY_'. PROP_MIN_PRICE_ID) {
		                                $arFilter[] = ['>=SINGLE.PROPERTY_'. PROP_MIN_PRICE_ID => $filterProp];
	                                }
                                }
                                else{
                                    $arSingleFilter['SINGLE.' . 'PROPERTY_' . $propId][] = $filterProp;
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($arMultiFilter))
            {
                $arFilter= ['LOGIC' => 'AND'];
                foreach($arMultiFilter as $multiProp)
                {
                    $arGroupProp = ['LOGIC' => 'OR'];
                    foreach ($multiProp as $prop)
                    {
                        $arGroupProp[] = $prop;
                    }
                    $arFilter[]= $arGroupProp;
                }
            }

            if(!empty($arSingleFilter))
                    $arFilter[] = $arSingleFilter;

            if(!empty($arFilter))
                $filter[] = $arFilter;

            if(!empty($this->arParams['FILTER']['=ID'])) $filter['=ID'] = $this->arParams['FILTER']['=ID'];

            $params = [
                    "order" => ['SINGLE.PROPERTY_' . PROP_AVAILABLE_ID => 'DESC', 'SORT2.SORT' => 'ASC', 'ID' => 'DESC'],
                    "count_total" => true,
                    "offset" => ($this->arParams['PAGE_NUM'] - 1) * $this->arParams['PAGE_SIZE'],
                    "limit" => $this->request->get('top') > 0 ? $this->arParams['PAGE_SIZE']*$this->request->get('top') : $this->arParams['PAGE_SIZE'],
                ];

            if ($this->isTrendSection())
            {
                $params['filter'] = [
                    "=IBLOCK_ID" => PRODUCTS_IBLOCK_ID,
                    'SORT2.TYPE' => 'nashy_trendy',
                    'SORT2.SECTION' => $this->arParams['FILTER']['PROPERTY_TREND'],
                    '>SINGLE.PROPERTY_'. PROP_AVAILABLE_ID => 0
                ];
                if (!empty($filter)) $params['filter'] = array_merge($params['filter'], $filter);
                $params['runtime'] = [
                    new \Bitrix\Main\Entity\ReferenceField(
                        'SORT2',
                        '\Aniart\Main\Orm\SortTable',
                        array(
                            '=this.ID' => 'ref.ITEM',
                        )
                    ),
                    'SINGLE' => [
                        'data_type' => $entityPropsSingle->getDataClass(),
                        'reference' => [
                            '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                        ],
                        'join_type' => 'inner'
                    ]
                ];
                if (!empty($multi)) {
                    foreach($multi as $prop)
                    {
                        $params['runtime']['MULTI'.$prop] = [
                            'data_type' => $entityPropsMulti->getDataClass(),
                            'reference' => [
                                '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                            ],
                            'join_type' => 'left'
                        ];
                    }
                    $params['group'] = ['ID'];
                }
            }
            elseif ($this->isSaleSection())
            {
                $params['filter'] = ["=IBLOCK_ID" => PRODUCTS_IBLOCK_ID,
                    'SORT2.TYPE' => 'sale',
                    '=SORT2.SECTION' => $this->arParams['FILTER']['SECTION_ID'],
                    '>SINGLE.PROPERTY_'.PROP_SALE_ID => 0,
                    '>SINGLE.PROPERTY_'. PROP_AVAILABLE_ID => 0
                ];
                if (!empty($filter)) $params['filter'] = array_merge($params['filter'], $filter);
                $params['runtime'] = [
                    new \Bitrix\Main\Entity\ReferenceField(
                        'SORT2',
                        '\Aniart\Main\Orm\SortTable',
                        array(
                            '=this.ID' => 'ref.ITEM',
                        )
                    ),
                    'SINGLE' => [
                        'data_type' => $entityPropsSingle->getDataClass(),
                        'reference' => [
                            '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                        ],
                        'join_type' => 'inner'
                    ]
                ];
                if (!empty($multi)) {
                    foreach($multi as $prop)
                    {
                        $params['runtime']['MULTI'.$prop] = [
                            'data_type' => $entityPropsMulti->getDataClass(),
                            'reference' => [
                                '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                            ],
                            'join_type' => 'left'
                        ];
                    }
                    $params['group'] = ['ID'];
                }

            }
            elseif ($this->isCatalogSection())
            {
                $params['filter'] = ["=IBLOCK_ID" => PRODUCTS_IBLOCK_ID,
                    'SORT2.TYPE' => 'catalog',
                    'SORT2.SECTION' => $this->arParams['FILTER']['SECTION_ID'],
                ];
                if(empty($filter['=ID']))
                    $params['filter']['>SINGLE.PROPERTY_'. PROP_AVAILABLE_ID] = 0;
                if (!empty($filter)) $params['filter'] = array_merge($params['filter'], $filter);
                $params['runtime'] = [
                    new \Bitrix\Main\Entity\ReferenceField(
                        'SORT2',
                        '\Aniart\Main\Orm\SortTable',
                        array(
                            '=this.ID' => 'ref.ITEM',
                        )
                    ),
                    'SINGLE' => [
                        'data_type' => $entityPropsSingle->getDataClass(),
                        'reference' => [
                            '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                        ],
                        'join_type' => 'inner'
                    ]
                ];
                if (!empty($multi)) {
                    foreach($multi as $prop)
                    {
                        $params['runtime']['MULTI'.$prop] = [
                            'data_type' => $entityPropsMulti->getDataClass(),
                            'reference' => [
                                '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                            ],
                            'join_type' => 'left'
                        ];
                    }
                    $params['group'] = ['ID'];
                }
            }
            $this->arResult['PRODUCTS'] = $this->productsOrmRepository->getList('\Bitrix\Iblock\ElementTable', $params, PRODUCTS_IBLOCK_ID);

            $nav = $this->productsOrmRepository->getLastNav();

            /*
             *страницы пагинации с несуществующим номером страницы, должны восприниматься как страницы с обычным get-параметром.
             * Таких страниц < 10%, поэтому делаю новый запрос, а не полючаю сначала общее количество.
             * */
            if( $this->arParams['PAGE_NUM'] > $nav->getPageCount())
            {
                $params['offset'] = 0;
                $this->arResult['PRODUCTS'] = $this->productsOrmRepository->getList('\Bitrix\Iblock\ElementTable', $params, PRODUCTS_IBLOCK_ID);
                $nav = $this->productsOrmRepository->getLastNav();

            }
            $pageNum = $this->arParams['PAGE_NUM'] > $nav->getPageCount() ? 1 : $this->arParams['PAGE_NUM'];
            $this->arResult['PAGINATION'] = [
                'NavNum' => $pageNum, //порядковый номер постранички на странице
                'NavPageCount' => $nav->getPageCount(), //количество страниц
                'NavPageNomer' => $pageNum, //номер текущей страницы
                'NavPageSize' => $this->arParams['PAGE_SIZE'], //количество элементов на странице
                'NavRecordCount' => $nav->getRecordCount(), //количество элементов в базе,
                'NavPageVar' => $this->arParams['PAGE_VAR']
            ];
        }
        else
        {
            $this->arParams['SORT'] = array_merge(['PROPERTY_AVAILABLE' => 'DESC'], $this->arParams['SORT']);
            $products = $this->productsRepository->getList(
                $this->arParams['SORT'],
                $this->arParams['FILTER'],
                false,
                [
                    'iNumPage' => $this->arParams['PAGE_NUM'],
                    'bShowAll' => false,
                    'nPageSize' => $pageSize
                ]
            );
            $this->arResult['PRODUCTS'] = array_values($products);
            if($dbResult = $this->productsRepository->getLastDBResult())
            {
                $this->arResult['DB_RESULT'] = $dbResult;
                $this->arResult['PAGINATION'] = [
                    'NavNum' => $dbResult->NavNum, //порядковый номер постранички на странице
                    'NavPageCount' => $dbResult->NavPageCount, //количество страниц
                    'NavPageNomer' => $dbResult->NavPageNomer, //номер текущей страницы
                    'NavPageSize' => $this->arParams['PAGE_SIZE'], //количество элементов на странице
                    'NavRecordCount' => $dbResult->NavRecordCount, //количество элементов в базе,
                    'NavPageVar' => $this->arParams['PAGE_VAR']
                ];
            }
        }

        \Aniart\Main\Seo\SeoPaging::init($this->arResult['PAGINATION']);
		$this->arResult['SECTION_ID'] =  $this->arParams['FILTER']['SECTION_ID'];
		$this->arResult['SECTION'] =  $this->sectionRepository->getById($this->arParams['FILTER']['SECTION_ID']);
		if(!empty($this->arResult['SECTION']))
		    $this->arResult['SECTION']->checkNoindex();
    }

    private function isTrendSection()
    {
        return $this->arParams['TYPE'] == 'nashy_trendy';
    }

    private function isSaleSection()
    {
        return $this->arParams['TYPE'] == 'sale';
    }

    private function isCatalogSection()
    {
        return $this->arParams['TYPE'] == 'catalog';
    }

    private function isSearchSection()
    {
        return !empty($_REQUEST['q']);
    }

	public function getSignedComponentParams()
	{
		$signer = new \Bitrix\Main\Security\Sign\Signer();
		return $signer->sign(base64_encode(serialize($this->arParams)), 'products.list');
	}
}