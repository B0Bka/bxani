<?php


namespace Aniart\Main\Ajax\Handlers;


use Aniart\Main\Ajax\AbstractAjaxHandler,
    Bitrix\Main\Security\Sign\BadSignatureException;

class CatalogAjaxHandler extends AbstractAjaxHandler
{
	protected $setPrice = false;
	private $filteredParams = [];
	public function loadPage()
	{
		$page = (int)$this->post['page'];
		if($page < 0){
			return $this->setError('Invalid page value "'.$page.'"');
		}
		try{
			$componentParams = $this->getComponentParamsFromRequest('products.list', 'componentParams');
			return $this->setOK(['html' => $this->getProductListHTML($componentParams)]);
		}
		catch (BadSignatureException $e){
			return $this->setError($e->getMessage());
		}

	}

	public function loadPageWithFilters()
	{
		$setPrice = false;
		$additionalParams = $this->post['additional_params'];
		$page = (int)$this->post['page'];
		if(isset($this->post['set_price']) && $this->post['set_price'] !== false){
			$setPrice = true;
		}

		if($page < 0){
			return $this->setError('Invalid page value "'.$page.'"');
		}
		try{
			$componentParams = $this->getComponentParamsFromRequest('products.list', 'componentParams');
			$filteredParams = \Aniart\Main\Services\Catalog\ComponentParams::rewriteParams($componentParams, $additionalParams, $setPrice);
			$this->filteredParams = $filteredParams;
			$filterCount = self::getActiveFiltersCount();
			return $this->setOK(['html' => $this->getProductListHTML($filteredParams), 'activeFilterCount'=>$filterCount]);
		}
		catch (BadSignatureException $e){
			return $this->setError($e->getMessage());
		}

	}

	public function loadPageWithFiltersMobile(){
		$data = $this->post["selectedValues"];
		try{
			$componentParams = $this->getComponentParamsFromRequest('products.list', 'componentParams');
			$filteredParams = \Aniart\Main\Services\Catalog\ComponentParams::rewriteFilterParamsByOnce($componentParams, $data);
			$this->filteredParams = $filteredParams;
			$filterCount = self::getActiveFiltersCount();

			return $this->setOK(['html' => $this->getProductListHTML($filteredParams), 'activeFilterCount'=>$filterCount]);
		}
		catch (BadSignatureException $e){
			return $this->setError($e->getMessage());
		}
	}

	private function getActiveFiltersCount(){
		$count = 0;
		foreach ($this->filteredParams["FILTER"] as $key=>$value){
			if(gettype($key) != 'integer') continue;
			$count ++;
		}
		return $count;
	}


	private function getProductListHTML($params)
	{
		global $APPLICATION;
		ob_start();
			$APPLICATION->IncludeComponent('aniart:products.list', 'main', $params);
			$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function getSetHTML()
	{
		global $APPLICATION;
		ob_start();
			$APPLICATION->IncludeComponent(
				'aniart:products.set',
				'popup',
				[
					'SET_ID' => intval($this->request['params']['id']),
					'AJAX_MODE' => 'N',
					'AJAX_OPTION_HISTORY' => 'N',
					'AJAX_OPTION_JUMP' => 'N',
					'CACHE_TYPE' => "N",
					'CACHE_TIME' => $arParams['CACHE_TIME'],
				],
				$component
			);
			$html = ob_get_contents();
		ob_end_clean();
		$this->setOK(['html' => $html]);
	}

	public function getProductDetailHTML()
	{
		global $APPLICATION;
		$productsRepository = app('ProductsRepository');
		$product = $productsRepository->getById(intval($this->request['params']['id']));
		$showSmallPics = $this->request['params']['small_pics'] != '' ?  $this->request['params']['small_pics'] : 'Y';
			$APPLICATION->IncludeComponent(
				'aniart:product.detail',
				'popup',
				[
					'ELEMENT_CODE' => $product->getCode(),
					'AJAX_MODE' => 'N',
					'AJAX_OPTION_HISTORY' => 'N',
					'AJAX_OPTION_JUMP' => 'N',
					'CACHE_TYPE' => "N",
					'CACHE_TIME' => $arParams['CACHE_TIME'],
                    'SHOW_SMALL_PICS' => $showSmallPics
				],
				$component
			);
			$html = ob_get_contents();
		ob_end_clean();
		$this->setOK(['html' => $html]);
	}
}