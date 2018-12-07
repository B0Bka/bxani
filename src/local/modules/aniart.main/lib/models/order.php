<?php


namespace Aniart\Main\Models;


use Aniart\Main\Interfaces\BasketInterface;
use Aniart\Main\Interfaces\PricebleInterface;
use Aniart\Main\Repositories\SaleDeliveriesRepository;
use Aniart\Main\Repositories\SaleDeliveryServicesRepository;
use Aniart\Main\Repositories\SalePaySystemsRepository;
use Aniart\Main\Ext\User;
use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Sale\Internals\OrderPropsTable;
use Bitrix\Sale\ShipmentCollection;

class Order extends AbstractModel implements PricebleInterface
{
    protected $fields;
    protected $propsValues;
    protected $propsMeta;
    /**
     * @var BasketInterface
     */
    protected $basket;
    protected $paySystem;
    protected $delivery;
    /**
     * @var Bitrix\Sale\PaymentCollection
     */
    protected $paymentCollection;

    public function __construct(array $fields = [])
    {
        if(isset($fields['BASKET_ITEMS'])){
            $this->initBasket($fields['BASKET_ITEMS']);
            unset($fields['BASKET_ITEMS']);
        }
        parent::__construct($fields);
    }

    public function setBasket(Basket $basket)
    {
        $this->basket = $basket;
        return $this;
    }

    public function getBasket()
    {
        if(is_null($this->basket)){
            $this->obtainBasket();
        }
        return $this->basket;
    }

    public function obtainBasket()
    {
        if(!$this->isNew()){
            $basketItems = app('BasketItemsRepository')->getList([], ['ORDER_ID' => $this->getId()]);
            $this->initBasket($basketItems);
        }
        return $this;
    }

    public function initBasket(array $basketItems)
    {
        $this->basket = app('Basket', [['BASKET_ITEMS' => $basketItems]]);
        return $this;
    }

    public function setPaySystemId($paySystemId)
    {
        $this->fields['PAY_SYSTEM_ID'] = $paySystemId;
        $this->paySystem = null;
        return $this;
    }

    public function setPaySystem(SalePaySystem $paySystem)
    {
        $this->paySystem = $paySystem;
        return $this;
    }

    /**
     * @return SalePaySystem|false
     */
    public function getPaySystem()
    {
        if(
            is_null($this->paySystem) &&
            ($paySystemId = $this->getPaySystemId())
        ){
            /**
             * @var SalePaySystemsRepository $paySystemsRepository
             */
            $paySystemsRepository =  app('PaySystemsRepository');
            $this->paySystem = $paySystemsRepository->getById($paySystemId);
        }
        return $this->paySystem;
    }

    public function getPaySystemId()
    {
        return $this->fields['PAY_SYSTEM_ID'];
    }

    public function setDeliveryId($deliveryId)
    {
        $this->fields['DELIVERY_ID'] = $deliveryId;
        $this->delivery = null;
        return $this;
    }

    public function setDelivery(SaleDelivery $delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    public function getDelivery()
    {
        if(
            is_null($this->delivery) &&
            ($deliveryId = $this->getDeliveryId())
        ){
            /**
             * @var SaleDeliveriesRepository $deliveriesRepository
             */
            $deliveriesRepository =  app('DeliveriesRepository');
            $this->delivery = $deliveriesRepository->getById($deliveryId);
            if(!$this->delivery){ //возможно имеем дело с профилем доставки
                /**
                 * @var SaleDeliveryServicesRepository $deliveryServicesRepository
                 */
                $deliveryServicesRepository = app('DeliveryServicesRepository');
                $this->delivery = $deliveryServicesRepository->getParentService($deliveryId);
            }
        }
        return $this->delivery;
    }

    public function getDeliveryId()
    {
        return $this->fields['DELIVERY_ID'];
    }

    public function getAmount()
    {
        $amount = $this->getPrice();
        if(!$this->isNew()){
            /**
             * @var \Bitrix\Sale\Order $bxOrder
             */
            $bxOrder = \Bitrix\Sale\Order::load($this->getId());
            if($bxOrder->getPaymentCollection()->count() > 0){
                $paymentSum = $bxOrder->getPaymentCollection()->getSum();
                if($paymentSum){
                    $amount = $paymentSum;
                }
            }
        }
        return $amount;
    }

    public function getUserId()
    {
        return $this->fields['USER_ID'];
    }

    public function getPrice($format = false)
    {
        $price = $this->getBasketPrice() + $this->getDeliveryPrice();
        return $this->formatPrice($price, $format);
    }

    public function getBasePrice($format = false)
    {
        return $this->formatPrice($this->getBasket()->getBasePrice(), $format);
    }

    public function getBasketPrice($format = false)
    {
        return $this->formatPrice($this->getBasket()->getPrice(), $format);
    }

    public function getDiscountPrice($format = false)
    {
        return $this->formatPrice($this->getBasket()->getDiscountPrice(), $format);
    }

    public function getDiscountSum($format = false)
    {
        return $this->formatPrice($this->getBasket()->getDiscountSum(), $format);
    }

    public function getDeliveryPrice($format = false)
    {
        return $this->formatPrice($this->fields['PRICE_DELIVERY'], $format);
    }

    protected function formatPrice($price, $format)
    {
        $price = round($price, CATALOG_VALUE_PRECISION);
        return $format ? SaleFormatCurrency($price, $this->getCurrency()) : (float)$price;
    }

    public function getCurrency()
    {
        $this->fields['CURRENCY'] = $this->fields['CURRENCY'] ?: $this->getBasket()->getCurrency();
        return $this->fields['CURRENCY'];
    }

    public function getPropertyValue($code)
    {
        $this->getPropsValues();
        return $this->propsValues[$code];
    }

    public function getPropsValues()
    {
        if(is_null($this->propsValues)){
            $this->obtainPropsValues();
        }
        return $this->propsValues;
    }

    /*
     * У каждой службы доставки своя адресная строка
     */
    public function getAdress()
    {
        $delivery = $this->getDeliveryId();
        $props = $this->getPropsValues();
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
        return $str;
    }

    public function getPropsMeta($primary = 'ID')
    {
        if(is_null($this->propsMeta)){
            $this->propsMeta = [];
            $rsProps = OrderPropsTable::getList();
            while($arProp = $rsProps->fetch()){
                $this->propsMeta[$arProp['ID']] = $arProp;
            }
        }
        if($primary == 'ID'){
            return $this->propsMeta;
        }
        return array_combine(array_map(function($propMeta) use ($primary){
            return isset($propMeta[$primary]) ? $propMeta[$primary] : $propMeta['ID'];
        }, $this->propsMeta), $this->propsMeta);
    }

    public function setPropsMeta(array $propsValues)
    {
        $this->propsMeta = array_combine(array_map(function($propValue){
            return $propValue['ID'];
        }, $propsValues), $propsValues);

        return $this;
    }

    public function getUserInfo()
    {
        $userInfo = \Aniart\Main\Ext\User::getInfo();
        $result = ['EMAIL' => $userInfo['EMAIL'], 'PHONE' => $userInfo['PERSONAL_PHONE'],
                    'NAME' => $userInfo['NAME'], 'LAST_NAME' => $userInfo['LAST_NAME']];
        return $result;
    }

    public function obtainPropsValues()
    {
        $this->propsValues = array();
        $rsPropsValues = \CSaleOrderPropsValue::GetOrderProps($this->getId());
        while($arPropValue = $rsPropsValues->Fetch()){
            $propCode = $arPropValue['CODE'];
            if(!$propCode){
                $propCode = $arPropValue['ID'];
            }
            $this->propsValues[$propCode] = $arPropValue['VALUE'];
        }
        return $this;
    }

    public function getAccountNumber()
    {
        return $this->fields['ACCOUNT_NUMBER'];
    }

    public function getCoupons() {
        DiscountCouponsManager::load();
        $coupons = DiscountCouponsManager::get();
        return $coupons;
    }

    /*
     * Для получения html службы оплати нужен объект \Bitrix\Sale\Payment
     */
    public function setPaymentCollection()
    {
        if ($order = \Bitrix\Sale\Order::loadByAccountNumber($this->getId()))
            $this->paymentCollection = $order->getPaymentCollection();
    }

    /*
     * Получить кнопку для обработчиков служб оплати
    */
    public function getPaymentContent()
    {
        $result = false;
        foreach ($this->paymentCollection as $payment)
        {
            $arResult["PAYMENT_ID"] = $payment->getId();

            if (intval($payment->getPaymentSystemId()) > 0 && !$payment->isPaid())
            {
                $paySystemService = \Bitrix\Sale\PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
                if (!empty($paySystemService))
                {
                    $arPaySysAction = $paySystemService->getFieldsValues();

                    if ($paySystemService->getField('NEW_WINDOW') === 'N' || $paySystemService->getField('ID') == \Bitrix\Sale\PaySystem\Manager::getInnerPaySystemId())
                    {

                        /** @var PaySystem\ServiceResult $initResult */
                        $initResult = $paySystemService->initiatePay($payment, null, \Bitrix\Sale\PaySystem\BaseServiceHandler::STRING);
                        if ($initResult->isSuccess())
                            $result = $initResult->getTemplate();
                    }
                }

            }
        }
        return $result;
    }

    /*
     * Получить ID оплаты
    */
    public function getPaymentId()
    {
        foreach ($this->paymentCollection as $payment)
        {
            if (intval($payment->getPaymentSystemId()) > 0 && !$payment->isPaid())
                return $payment->getId();
        }
    }

    /*
     * Получить ID оплаты
    */
    public function getOrderPayed()
    {
        if ($order = \Bitrix\Sale\Order::loadByAccountNumber($this->getId()))
            return $order->isPaid();
    }
}