<?php

use Aniart\Main\Models\Product;
use Bitrix\Main\Entity\ReferenceField;
use \Bitrix\Main\DB\SqlExpression;

class AniartProductsListComponent extends CBitrixComponent
{
    /**
     * @var  \Aniart\Main\Repositories\ProductsRepository;
     */
    protected $productsOrmRepository;
    protected $trendsRepository;
    protected $productSectionsRepository;
	/**
	 * @var \Bitrix\Main\HttpRequest
	 */
    protected $request;

    public function __construct($component = null)
    {
        parent::__construct($component);

        $this->productsOrmRepository = app('ProductsOrmRepository');
        $this->trendsRepository = app('TrendSectionRepository');
        $this->productSectionsRepository = app('ProductSectionsRepository');
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE'] ?: 'A';
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ?: 36000;
        $arParams['FILTER'] = array_merge((array)$arParams['FILTER'], [
            'ACTIVE' => 'Y',
            'SECTION_ID' => $arParams['SECTION_ID']
        ]);
        $arParams['SORT'] = $arParams['SORT'] ?: ['DATE_CREATED' => 'DESC'];
	    $arParams['PAGE_VAR'] = $arParams['PAGE_VAR'] ?: 'page';
        $arParams['PAGE_SIZE'] = $arParams['PAGE_SIZE'] ?: 10;
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
        $this->arParams['SORT'] = array_merge(['PROPERTY_AVAILABLE' => 'DESC'], $this->arParams['SORT']);
		$this->arResult['PRODUCTS'] = [];
		$this->arResult['SECTION'] = $this->request->get('id');
		$this->arResult['TYPE'] = $this->request->get('type');
		$this->arResult['PAGE'] = $this->arParams['PAGE_NUM'] > 0 ? $this->arParams['PAGE_NUM'] : 1;

        $params = [
           'order' => ['SORT2.SORT' => 'ASC', 'ID' => 'DESC'],
           'limit' => $this->arParams['PAGE_SIZE'],
           'count_total' => true,
           'offset' => 0//($this->arResult['PAGE'] - 1) * $this->arParams['PAGE_SIZE'],
        ];
		if($this->isTrendSection())
        {
            $trend = $this->trendsRepository->getById($this->arResult['SECTION']);
            $this->arResult['TITLE'] = i18n('TRANDS').' - '.$trend->getName();

            $entityPropsMulti = Bitrix\Main\Entity\Base::compileEntity(
                sprintf('PROPS_MULTI_%i', PRODUCTS_IBLOCK_ID),
                [
                    'IBLOCK_ELEMENT_ID' => ['data_type' => 'integer'],
                    'IBLOCK_PROPERTY_ID' => ['data_type' => 'integer'],
                    'VALUE' => ['data_type' => 'integer']
                ],
                [
                    'table_name' => 'b_iblock_element_prop_m'.PRODUCTS_IBLOCK_ID,
                ]
            );

            $params['filter'] = array("=IBLOCK_ID"=>PRODUCTS_IBLOCK_ID, 'SORT2.TYPE' => 'nashy_trendy', 'SORT2.SECTION' => $this->arResult['SECTION'], 'TREND.VALUE' => $this->arResult['SECTION']);
            $params['runtime'] = [
                             new \Bitrix\Main\Entity\ReferenceField(
                                'SORT2',
                                '\Aniart\Main\Orm\SortTable',
                                array(
                                    '=this.ID' => 'ref.ITEM',
                                )
                            ),
                           'TREND' => [
                                'data_type' => $entityPropsMulti->getDataClass(),
                                'reference' => [
                                    '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                                    'ref.IBLOCK_PROPERTY_ID' => new  \Bitrix\Main\DB\SqlExpression('?', PROP_TREND_ID   ),
                                ],
                                'join_type' => 'left'
                           ]
                   ];
        }
        elseif($this->isSaleSection())
        {
            $this->arResult['TITLE'] = i18n('SALE');
            if(!empty($this->arResult['SECTION']))
            {
                $section = $this->productSectionsRepository->getById($this->arResult['SECTION']);
                $this->arResult['TITLE'] .= ' - '.$section->getName();
            }
            $entityPropsSingle = Bitrix\Main\Entity\Base::compileEntity(
                sprintf('PROPS_SINGLE_%s', PRODUCTS_IBLOCK_ID),
                [
                    'IBLOCK_ELEMENT_ID' => ['data_type' => 'integer'],
                    'PROPERTY_'.PROP_SALE_ID => ['data_type' => 'integer'],
                ],
                [
                    'table_name' => sprintf('b_iblock_element_prop_s%s', PRODUCTS_IBLOCK_ID),
                ]
            );
            $params['filter'] = ["=IBLOCK_ID"=>PRODUCTS_IBLOCK_ID, 'SORT2.TYPE' => 'sale', 'SORT2.SECTION' => $this->arResult['SECTION'], '>SALE.PROPERTY_'.PROP_SALE_ID => 0];
            $params['runtime'] = [
                            new \Bitrix\Main\Entity\ReferenceField(
                                'SORT2',
                                '\Aniart\Main\Orm\SortTable',
                                array(
                                    '=this.ID' => 'ref.ITEM',
                                )
                            ),
                           'SALE' => [
                                'data_type' => $entityPropsSingle->getDataClass(),
                                'reference' => [
                                    '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                                ],
                                'join_type' => 'left'
                           ],
                   ];
        }
        else
        {
            $this->arResult['TITLE'] = i18n('CATALOG');
            if(!empty($this->arResult['SECTION']))
            {
                $section = $this->productSectionsRepository->getById($this->arResult['SECTION']);
                $this->arResult['TITLE'] .= ' - '.$section->getName();
            }

            $params['filter'] = ["=IBLOCK_ID"=>PRODUCTS_IBLOCK_ID, 'SORT2.TYPE' => 'catalog', 'SORT2.SECTION' => $this->arResult['SECTION']];
            $params['runtime'] = [
                            new \Bitrix\Main\Entity\ReferenceField(
                                'SORT2',
                                '\Aniart\Main\Orm\SortTable',
                                array(
                                    '=this.ID' => 'ref.ITEM',
                                )
                            ),
                   ];
        }
        $this->arResult['PRODUCTS'] = $this->productsOrmRepository->getList('\Bitrix\Iblock\ElementTable', $params, PRODUCTS_IBLOCK_ID);
    }

    private function isTrendSection()
    {
        return $this->arResult['TYPE'] == 'nashy_trendy';
    }

    private function isSaleSection()
    {
        return $this->arResult['TYPE'] == 'sale';
    }


	public function getSignedComponentParams()
	{
		$signer = new \Bitrix\Main\Security\Sign\Signer();
		return $signer->sign(base64_encode(serialize($this->arParams)), 'products.list');
	}
}