<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader,
	Bitrix\Main\Error,
	Bitrix\Main\ErrorCollection,
	Bitrix\Highloadblock as HL,
	Bitrix\Sale,
	Bitrix\Sale\Basket,
	Bitrix\Sale\Fuser,
	Bitrix\Sale\DiscountCouponsManager,
	Bitrix\Sale\PriceMaths,
	Bitrix\Iblock,
	Bitrix\Catalog;

CBitrixComponent::includeComponentClass('bitrix:sale.basket.basket');

class CAniartBasketComponent extends CBitrixBasketComponent
{
	const IMAGE_SIZE_STANDARD = 270;
	const IMAGE_SIZE_ADAPTIVE = 270;

    protected function recalculateAjaxAction()
	{
	    if($this->request->get('clearBasket') == 'Y')
	        $result = $this->clearBasket();
	    else
		    $result = $this->recalculateBasket($this->request->get('basket'));

		list($basketRefreshed, $changedBasketItems) = $this->refreshAndCorrectRatio();
		$result['BASKET_REFRESHED'] = $basketRefreshed;
		$result['CHANGED_BASKET_ITEMS'] = array_merge($result['CHANGED_BASKET_ITEMS'], $changedBasketItems);

		$this->saveBasket();
		$this->modifyResultAfterSave($result);

		if (
			!empty($result['APPLIED_DISCOUNT_IDS'])
			|| implode(',', $result['APPLIED_DISCOUNT_IDS']) !== $this->request->get('lastAppliedDiscounts')
			|| $this->request->get('fullRecalculation') === 'Y'
		)
		{
			// reload all items
			$this->loadBasketItems();
		}
		else
		{
			$this->loadBasketItems($result['CHANGED_BASKET_ITEMS']);
		}

		$result['BASKET_DATA'] = $this->getBasketResult();

		if ($this->needToReloadGifts($result))
		{
			$result['GIFTS_RELOAD'] = true;
		}

		self::sendJsonAnswer($result);
	}
	private function clearBasket()
    {
        $basketItemsRepository = app('BasketItemsRepository');
        $basketItems = app('Basket', [['BASKET_ITEMS'=>$basketItemsRepository->getList(
            ['ID'=>'ASC'],
            [
                'FUSER_ID'=>CSaleBasket::GetBasketUserID(),
                'LID'=>SITE_ID,
                'ORDER_ID'=>'NULL'
            ]
        )]]);
        $basket = $basketItems->getItems();
        foreach($basket as $item)
        {
            $result['CHANGED_BASKET_ITEMS'][] =  $item->getId();
            $result['DELETED_BASKET_ITEMS'][] =  $item->getId();
        }
        \CSaleBasket::DeleteAll(\CSaleBasket::GetBasketUserID());
        $result['DELETE_ORIGINAL'] = 'Y';
        $result['BASKET_REFRESHED'] = 'Y';
        return $result;
    }

}