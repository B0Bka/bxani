<?php
namespace Aniart\Main\Services\Catalog;

use Aniart\Main\Traits\ProductPropCodeTrait;

class ProductRecommendation extends Service
{
    use ProductPropCodeTrait;

    protected $productId;
    protected $propCode;
    protected $recommended;

    public function __construct($arFields)
    {
        parent::__construct();
        $this->productId = $arFields['ID'];
        $this->propCode = $this->getPropCode('recommended_product');
        $this->recommended = $arFields['PROPERTY_VALUES'][54];
    }

    public function init()
    {
        $arValue = [];
        if(!empty($this->recommended)) //если редактируется рекомендуемые. Иначе свойство затрется
        {
            foreach($this->recommended as $recomended)
            {
                if(is_array($recomended) && $recomended['VALUE'] > 0)
                    $arValue[] = $recomended['VALUE'];
                elseif(intval($recomended) > 0)
                    $arValue[] = $recomended;
            }
            if(!empty($arValue))
            {
                foreach($arValue as $key => $val)
                {
                    $VALUES = array();
                    $res = \CIBlockElement::GetProperty(PRODUCTS_IBLOCK_ID, $val, "sort", "asc", array("CODE" => $this->propCode));
                    while ($ob = $res->GetNext())
                    {
                        $VALUES[] = $ob['VALUE'];
                    }

                    /*
                     * Обновить значение
                     */
                    if(!in_array($this->productId, $VALUES))
                    {
                        $VALUES[] = $this->productId;
                        $update = \CIBlockElement::SetPropertyValueCode($val, $this->propCode, $VALUES);
                    }
                }
            }

            $arSelect = Array("ID", "PROPERTY_".$this->propCode);
            $arFilter = Array("IBLOCK_ID"=>PRODUCTS_IBLOCK_ID, "PROPERTY_".$this->propCode=>$this->productId);
            $res = \CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, Array("nPageSize"=>500), $arSelect);
            while($ob = $res->GetNextElement())
            {
                $arRecomended = $ob->GetFields();
                if(!in_array($arRecomended['ID'], $arValue))
                {
                    $recValues = $arRecomended["PROPERTY_".$this->propCode."_VALUE"];
                    $key = array_search($this->productId, $recValues);
                    unset($recValues[$key]);
                    $update = \CIBlockElement::SetPropertyValueCode($arRecomended['ID'], $this->propCode, $recValues);
                }
            }
        }
    }
}