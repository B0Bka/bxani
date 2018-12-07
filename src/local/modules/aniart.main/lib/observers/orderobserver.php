<?php
namespace Aniart\Main\Observers;

class OrderObserver
{
    public function OnSaleOrderSavedHandler(\Bitrix\Main\Event $event)
    {
        $arFields = ['EMAIL', 'NAME', 'LAST_NAME', 'PERSONAL_PHONE', 'SECOND_NAME'];
        $order = $event->getParameter("ENTITY");
        $propertyCollection = $order->getPropertyCollection();
        $arProps = $propertyCollection->getArray();
        $rsUser = \CUser::GetByID($order->getUserId());
        $arUser = $rsUser->Fetch();
        foreach($arProps['properties'] as $prop)
            $orderProps[$prop['CODE']] = current($prop['VALUE']);

        //установка свойства ФИО для retailCrm
        $propFio = $propertyCollection->getItemByOrderPropertyId(1);
        if(!empty($propFio))
        {
            $propFio->setValue($orderProps['LAST_NAME']." ".$orderProps['NAME']);
            $propFio->save();
        }
        $orderProps['PERSONAL_PHONE'] = $orderProps['PHONE'];
        foreach($arFields as $field)
            if(empty($arUser[$field])) $arUpdate[$field] = $orderProps[$field];
        if(!empty($arUpdate))
        {
            $user = new \CUser;
            if(!$user->Update($order->getUserId(), $arUpdate)) return $user->LAST_ERROR;
                else return true;
        }
    }
}
