
<?foreach($arResult['~PAY_SYSTEM'] as $paySystem){
    if($paySystem->NAME == 'Наличный расчет') $paySystem->NAME = 'Наличными при получении';
    if($paySystem->NAME == 'Оплата кредитной картой') $paySystem->NAME = 'On-line картой';
}?>