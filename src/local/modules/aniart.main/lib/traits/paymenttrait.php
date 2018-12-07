<?php
namespace Aniart\Main\Traits;

trait PaymentTrait
{
    private $paymentStorage = [
        2 => ['name' => 'Наличный расчет', 'variants' => ['наличные', 'фискальный чек', 'пост-финанс']],
        3 => ['name' => 'Оплата кредитной картой', 'variants' => ['эквайринг', 'эквайринг-бонус-плюс', 'корпоративный клиент']],
        4 => ['name' => 'Cертификат', 'variants' => ['сертификат']]
    ];

    public function getPaymentId($code1C)
    {
        $code = $this->normalizeCode($code1C);
        foreach($this->paymentStorage as $paymentId => $arCodes)
        {
            if(in_array($code, $arCodes['variants'])) return ['id' => $paymentId, 'name' => $arCodes['name']];
        }
    }

    private function normalizeCode($code)
    {
        return mb_strtolower($code);
    }
}