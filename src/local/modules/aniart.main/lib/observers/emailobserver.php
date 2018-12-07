<?
namespace Aniart\Main\Observers;

class EmailObserver
{
    function OnBeforeEventAddHandler(&$event, &$lid, &$arFields)
    {
        if ($event == 'SALE_NEW_ORDER')
        {
            $order = \Bitrix\Sale\Order::load($arFields['ORDER_ID']);
            if(self::get1cOrder($order)) $arFields['EMAIL'] = ''; // не отправлять, если заказ импортирован с 1с

            $basket = $order->getBasket();
            $items = '';
            foreach ($basket as $basketItem)
            {
                $name = $basketItem->getField('NAME');
                $price = SaleFormatCurrency($basketItem->getField('PRICE'), $basketItem->getField('CURRENCY'));
                $url = $basketItem->getField('DETAIL_PAGE_URL');
                $items .= '<a href="'.$url.'" target="_blank">'.$name.'</a> - '.$price.'<br/>';
            }
            $arFields['ORDER_LIST'] = $items;

            $paymentCollection = $order->getPaymentCollection();
            foreach ($paymentCollection as $payment) $arFields['PAYMENT_INFO'] = $payment->getPaymentSystemName();

            $shipmentCollection = $order->getShipmentCollection();
            foreach ($shipmentCollection as $shipment)
            {
                $deliveryName = $shipment->getDeliveryName();
                $deliveryId = $shipment->getDeliveryId();
            }

            if(substr_count($deliveryName, 'отделение') > 0)
                $deliveryName = str_replace(' отделение', '', $deliveryName);
            $deliveryAdress = self::getAdress($order, $deliveryId);
            $arFields['DELIVERY_INFO'] = $deliveryName.' '.$deliveryAdress;
        }
        elseif (substr_count($event, 'SALE_STATUS_CHANGED') > 0 && !empty($arFields['ORDER_REAL_ID']))
        {
            $order = \Bitrix\Sale\Order::load($arFields['ORDER_REAL_ID']);
            if(self::get1cOrder($order)) $arFields['EMAIL'] = ''; // не отправлять, если заказ импортирован с 1с
        }
    }

    public function getAdress($order, $delivery)
    {
        $propertyCollection = $order->getPropertyCollection();
        $arProps = $propertyCollection->getArray();
        foreach($arProps['properties'] as $prop)
        {
            $props[$prop['CODE']] = reset($prop['VALUE']);
        }
        switch ($delivery)
        {
            case 2: $arr = [mbLcFirst($props['NP_DEPARTMENT']), $props['CITY']];
                break;
            case 3: $arr = [$props['CITY'], $props['STREET'], $props['HOUSE'], $props['APARTMENT']];
                break;
            case 5: $arr = [$props['CITY'], $props['STORE_ADDRESS']];
                break;
            default: $arr = [$props['CITY']];
                break;
        }
        $str = implode(', ', $arr);
        if(!empty($props['PHONE'])) $str .= '<br/>'.$props['PHONE'];
        return $str;
    }

    private function get1cOrder($obOrder)
    {
        $propertyCollection = $obOrder->getPropertyCollection();
        $storeProp = $propertyCollection->getItemByOrderPropertyId(17);
        $import1cDate = $storeProp->getValue();
        return !empty($import1cDate);
    }
}


