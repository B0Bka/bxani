<?
foreach($arResult['~DELIVERY'] as $delivery){
    if($delivery->isChecked()) $currDelivery = $delivery;
}
foreach($arResult['~PAY_SYSTEM'] as $paySystem){
    if($paySystem->NAME == 'Наличный расчет' && $currDelivery->isNewPostStores()) $paySystem->NAME = 'Наличными при получении (Наложенный платеж)';
    if($paySystem->NAME == 'Наличный расчет') $paySystem->NAME = 'Наличными при получении';
    if($paySystem->NAME == 'Оплата кредитной картой') $paySystem->NAME = 'Банковской картой на сайте (Visa, Mastercard)';
}?>