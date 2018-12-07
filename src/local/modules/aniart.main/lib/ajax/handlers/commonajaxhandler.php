<?php

namespace Aniart\Main\Ajax\Handlers;

use Aniart\Main\Ajax\AbstractAjaxHandler;
use Bitrix\Main\Localization\Loc;
use Aniart\Main\Repositories\ProductSectionsRepository;
use Aniart\Main\Repositories\BannerSectionRepository;
use Aniart\Main\Repositories\TrendSectionRepository;

Loc::loadMessages(__FILE__);

class CommonAjaxHandler extends AbstractAjaxHandler
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function getFunction()
    {
        return $this->request['func'];
    }

    public function addError()
    {
        $text = $this->request['text'];
        $url = $this->request['url'];
        if(!empty($text) && !empty($url))
        {
            $error = new \Aniart\Main\Repositories\HandbooksRepository(HL_ERRORS_ID);
            $error->add(["UF_URL" => $url, "UF_TEXT" => $text]);
            $this->setOK(array('json' => $this->request));
        }
    }

	/*
	 * Получить ссылку на раздел баннеров в админке.
	 * Если такого раздела нет, то создать.
	 */
    public function getBannersection()
	{
		$catalogId = $this->request['params']['catalogSection'];
		$sectionId = $this->request['params']['section'];
		$trendId = $this->request['params']['trendSection'];
        $type = $this->request['params']['type'];
		/*
		 * проверка на существование раздела баннеров (защита от повторного создания)
		 */

		if($catalogId > 0)
        {
            $bannerSectionRepository = new BannerSectionRepository(BANNERS_IBLOCK_ID);
            $section = $bannerSectionRepository->getByCatalogSection($catalogId, $type);
            if($section) $sectionId = $section->getId();
        }

		if($sectionId > 0)
		{
			$url = '/bitrix/admin/iblock_element_admin.php?IBLOCK_ID='.BANNERS_IBLOCK_ID.'&type=content&lang=ru&find_section_section='.$sectionId;
			$this->setOK(['url' => $url]);
		}
		elseif($catalogId > 0)
		{
			$sectionRepository = new ProductSectionsRepository(PRODUCTS_IBLOCK_ID);
			$sections = $sectionRepository->getList([],['ID' => $catalogId]);

			$name = reset($sections)->getName();
			if($type != 'catalog') $name = $name.' '.$type;
			$bs = new \CIBlockSection;
			$arFields = Array(
			  "IBLOCK_ID" => BANNERS_IBLOCK_ID,
			  "NAME" => $name,
			  "UF_SECTION" => $catalogId,
              "UF_TYPE" => $type
			  );
			$ID = $bs->Add($arFields);
			if($ID <= 0)  $this->setError($bs->LAST_ERROR);
			else
			{
				$url = '/bitrix/admin/iblock_element_admin.php?IBLOCK_ID='.BANNERS_IBLOCK_ID.'&type=content&lang=ru&find_section_section='.$ID;
				$this->setOK(['url' => $url]);
			}
		}
		elseif($trendId > 0)
        {
			$sectionRepository = new TrendSectionRepository(TREND_MENU_IB);
			$sections = $sectionRepository->getList([],['ID' => $trendId]);

			$name = reset($sections)->getName();
			if($type != 'catalog') $name = $name.' '.$type;
			$bs = new \CIBlockSection;
			$arFields = Array(
			  "IBLOCK_ID" => BANNERS_IBLOCK_ID,
			  "NAME" => $name,
			  "UF_TREND" => $trendId,
              "UF_TYPE" => $type
			  );
			$ID = $bs->Add($arFields);
			if($ID <= 0)  $this->setError($bs->LAST_ERROR);
			else
			{
				$url = '/bitrix/admin/iblock_element_admin.php?IBLOCK_ID='.BANNERS_IBLOCK_ID.'&type=content&lang=ru&find_section_section='.$ID;
				$this->setOK(['url' => $url]);
			}
        }
	}

    public function getInstagramProduct()
    {
    	global $APPLICATION;
    	ob_start();

    	$id = $this->request['id_post'];
    	$GLOBALS['arrFilterInstagram'] = array(
    		'=ID'=> $id,
		);
    	$APPLICATION->IncludeComponent(
    			"bitrix:news.list",
    			"window",
    			array(
    					"IBLOCK_TYPE" => "content",
    					"IBLOCKS" => INSTAGRAM_IBLOCK_ID,
    					"NEWS_COUNT" => "1",
    					"FIELD_CODE" => array(
    							0 => "",
    							1 => "4",
    							2 => "Инстаграм",
    							3 => "",
    					),
    					"SORT_BY1" => "ACTIVE_FROM",
    					"SORT_ORDER1" => "DESC",
    					"SORT_BY2" => "SORT",
    					"SORT_ORDER2" => "ASC",
    					"DETAIL_URL" => "news_detail.php?ID=#ELEMENT_ID#",
    					"ACTIVE_DATE_FORMAT" => "d.m.Y",
    					"CACHE_TYPE" => "A",
    					"CACHE_TIME" => "300",
    					"CACHE_GROUPS" => "Y",
    					"COMPONENT_TEMPLATE" => "instagram",
    					"IBLOCK_ID" => "4",
    					"FILTER_NAME" => "arrFilterInstagram",
    					"PROPERTY_CODE" => array(
    							0 => "",
    							1 => "",
    					),
    					"CHECK_DATES" => "Y",
    					"AJAX_MODE" => "N",
    					"AJAX_OPTION_JUMP" => "N",
    					"AJAX_OPTION_STYLE" => "Y",
    					"AJAX_OPTION_HISTORY" => "N",
    					"AJAX_OPTION_ADDITIONAL" => "",
    					"CACHE_FILTER" => "N",
    					"PREVIEW_TRUNCATE_LEN" => "",
    					"SET_TITLE" => "Y",
    					"SET_BROWSER_TITLE" => "Y",
    					"SET_META_KEYWORDS" => "Y",
    					"SET_META_DESCRIPTION" => "Y",
    					"SET_LAST_MODIFIED" => "N",
    					"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
    					"ADD_SECTIONS_CHAIN" => "Y",
    					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
    					"PARENT_SECTION" => "",
    					"PARENT_SECTION_CODE" => "",
    					"INCLUDE_SUBSECTIONS" => "Y",
    					"STRICT_SECTION_CHECK" => "N",
    					"PAGER_TEMPLATE" => ".default",
    					"DISPLAY_TOP_PAGER" => "N",
    					"DISPLAY_BOTTOM_PAGER" => "Y",
    					"PAGER_TITLE" => "Новости",
    					"PAGER_SHOW_ALWAYS" => "N",
    					"PAGER_DESC_NUMBERING" => "N",
    					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    					"PAGER_SHOW_ALL" => "N",
    					"PAGER_BASE_LINK_ENABLE" => "N",
    					"SET_STATUS_404" => "N",
    					"SHOW_404" => "N",
    					"MESSAGE_404" => ""
    			),
    			false
		);

    	$html = ob_get_contents();

			//$html = 'test';

    	ob_end_clean();
    	$this->setOK($html);
    }

    public function setShowSortTrends($params)
    {
        if($this->request['params']['val'] == 'Y')
            \COption::SetOptionString("aniart.main", "show_trends_sort", "N");
        else
            \COption::SetOptionString("aniart.main", "show_trends_sort", "Y");
        $this->setOK(['success' => 'Y']);
    }

    public function setShowFilter()
    {
        $dir = $this->request['params']['dir'];
        if($this->request['params']['val'] == 'Y')
            \COption::SetOptionString("aniart.main", "show_filter_".$dir, "N");
        else
            \COption::SetOptionString("aniart.main", "show_filter_".$dir, "Y");
        $this->setOK(['success' => 'Y']);
    }

    public function addMeta()
    {
    	global $APPLICATION;
    	ob_start();

    	$APPLICATION->IncludeComponent(
            "aniart:meta.form",
            "",
            array(
                'URL' => $this->request['params']['url']
            ),
            false
        );
    	$html = ob_get_contents();

    	ob_end_clean();
    	$this->setOK($html);
    }

    public function runFeed()
    {
        if($this->request['run'] == 'y')
        {
            foreach($this->request['items'] as $item) \COption::SetOptionString("aniart", "run_".$item, "Y");
            $this->setOK('Ok');
        }
    }

    public function runSale()
    {
        if($this->request['run'] == 'y')
        {
            \COption::SetOptionString("aniart","run_sale","Y");
            $this->setOK('Ok');
        }
    }

    public function showFeedBackForm()
    {
    	global $APPLICATION;
    	ob_start();

    	$APPLICATION->IncludeComponent(
            "aniart:feedback.form",
            "popup",
            array(),
            false
        );
    	$html = ob_get_contents();

    	ob_end_clean();
    	$this->setOK($html);
    }

    public function showSubscribeButton()
    {
    	global $APPLICATION;
    	ob_start();

        $APPLICATION->IncludeComponent(
            'aniart:subscribe.button',
            'main',
            [
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => 3600000
            ]
        );
    	$html = ob_get_contents();

    	ob_end_clean();
    	$this->setOK($html);
    }
}
