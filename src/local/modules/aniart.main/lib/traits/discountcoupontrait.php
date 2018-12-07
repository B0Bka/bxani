<?php
namespace Aniart\Main\Traits;

trait DiscountCouponTrait
{
    public function generateCoupon()
    {
        $COUPON = \CatalogGenerateCoupon();
        echo DISCOUNT_SUBSCRIBE_ID.' ';
        $arCouponFields = array(
            "DISCOUNT_ID" => DISCOUNT_SUBSCRIBE_ID,
            "ACTIVE" => "Y",
            "MAX_USE"=> 1,
            "COUPON" => $COUPON,
            "TYPE" => 2,
            "DATE_APPLY" => false
        );

        $CID = \Bitrix\Sale\Internals\DiscountCouponTable::add($arCouponFields);
        if(!empty($CID)) return $COUPON;
            else return false;
    }
}