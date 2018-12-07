<?php


namespace Aniart\Main\Traits;


trait ProductPropCodeTrait
{
    private $codeStorage = [
        'article' => 'CML2_ARTICLE',
        'more_photo' => 'MORE_PHOTO',
        'more_photo_elite' => 'MORE_PHOTO_ELITE',
        'max_price' => 'MAX_PRICE',
        'min_price' => 'MIN_PRICE',
        'color' => 'COLOR',
        'size' => 'SIZES', 
        'collection' => 'COLLECTION',
        'name_show' => 'NAME_SHOW',
        'siblings' => 'SIBLINGS',
        'recommended_product' => 'RECOMMENDED_PRODUCT',
        'season' => 'SEASON',
        'more_photo_preview' => 'MORE_PHOTO_PREVIEW',
        'more_photo_elite_preview' => 'MORE_PHOTO_ELITE_PREVIEW'
    ];

    private $defaultPropValues = [
        'CLASP' => 'без застежки',
        'LINING_MATERIAL' => 'без подкладки',
        'FILLER_MATERIAL' => 'без утеплителя',
    ];

    public function getPropCode($code)
    {
        return $this->codeStorage[$code];
    }
    
    public function getCodeStorage()
    {
        return $this->storage;
    }
}