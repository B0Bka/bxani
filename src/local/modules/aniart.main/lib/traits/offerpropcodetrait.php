<?php

namespace Aniart\Main\Traits;

trait OfferPropCodeTrait
{
    private $codeStorage = [
        'link' => 'CML2_LINK',
        'article' => 'CML2_ARTICLE',
        'more_photo' => 'MORE_PHOTO',
        'size' => 'SIZE', 
        'global_status' => 'GLOBAL_STATUS', 
        'status' => 'STATUS'
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