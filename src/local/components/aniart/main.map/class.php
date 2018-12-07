<?php

use Aniart\Main\Models\Product;

class AniartProductsListComponent extends CBitrixComponent
{
    /**
     * @var  \Aniart\Main\Repositories\ProductSectionsRepository;
     */
    protected $productSectionsRepository;
    protected $saleMenuSectionsRepository;
    protected $trendSectionRepository;
    protected $block;

    public function __construct($component = null)
    {
        parent::__construct($component);

        $this->productSectionsRepository = app('ProductSectionsRepository');
        $this->saleMenuSectionsRepository = app('SaleMenuSectionsRepository');
        $this->trendSectionRepository = app('TrendSectionRepository');
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
        $this->initCatalog();
        $this->initSale();
        $this->initTrends();
        $this->initPersonal();
        $this->initContent();
        $this->arResult = $this->block;

        if($this->StartResultCache())
        {
	        $this->IncludeComponentTemplate();
        }
    }

    private function initCatalog()
    {
        $sections = $this->productSectionsRepository->getList(
            ['ID' => 'ASC'],
            ['ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y']
        );
        foreach($sections as $section)
        {
            $parent = $section->getParentId();
            if($parent > 0)
                $arSection[$parent]['ITEMS'][$section->getId()] = ['NAME' => $section->getName(), 'URL' => $section->getUrl()];
            else
                $arSection[$section->getId()] = ['NAME' => $section->getName(), 'URL' => $section->getUrl()];
        }
        $this->block['CATALOG']['SECTIONS'] = $arSection;
    }
    private function initSale()
    {
        $arSection = ['NAME' => i18n('SALE', 'sitemap'), 'URL' => '/sale/', 'ITEMS' => []];
        $sections = $this->saleMenuSectionsRepository->getList(
            ['ID' => 'ASC'],
            ['ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y']
        );
        foreach($sections as $section)
        {
            $arSection['ITEMS'][$section->getId()] = ['NAME' => $section->getName(), 'URL' => $section->getPropertyValue('LINK')];
        }
        $this->block['CATALOG']['SECTIONS']['SALE'] = $arSection;
    }
    private function initTrends()
    {
        $arSection = ['NAME' => i18n('TRENDS','sitemap'), 'URL' => '/nashy_trendy/', 'ITEMS' => []];
        $sections = $this->trendSectionRepository->getList(
            ['ID' => 'ASC'],
            ['ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y']
        );
        foreach($sections as $section)
        {
            $parent = $section->getParentId();
            if($parent > 0)
                $arSection['ITEMS'][$parent]['ITEMS'][$section->getId()] = ['NAME' => $section->getName(), 'URL' => $section->getUrl()];
            else
                $arSection['ITEMS'][$section->getId()] = ['NAME' => $section->getName(), 'URL' => $section->getUrl()];
        }
        $this->block['CATALOG']['SECTIONS']['TRENDS'] = $arSection;
    }
    private function initPersonal()
    {
        $this->block['PERSONAL'] = $this->getMenu(['m_personal']);
    }

    private function initContent()
    {
        $this->block['CONTENT'] = $this->getMenu(['m_company', 'm_service']);
    }

    private function getMenu($arType)
    {
        $arMenu = [];
        foreach($arType as $type)
        {
            $aMenuLinks = [];
            $arNormalize = [];
            $file = $_SERVER["DOCUMENT_ROOT"].'/.'.$type.'.menu.php';
            if(file_exists($file))
            {
                include($file);
                foreach($aMenuLinks as $item)
                {
                    if(empty($item[3]['AUTH'])) $arNormalize[] = ['NAME' => $item[0], 'URL' => $item[1]];
                }
                $arMenu = array_merge($arMenu, $arNormalize);
            }
            //$arMenu
        }
        return $arMenu;
    }
}