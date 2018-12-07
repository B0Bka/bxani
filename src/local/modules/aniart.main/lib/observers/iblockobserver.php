<?php

namespace Aniart\Main\Observers;

use Aniart\Main\Services\Catalog\ProductRecommendation;

class IblockObserver
{
    public function onAfterIBlockElementUpdate($arFields)
    {
        if($arFields['IBLOCK_ID'] == PRODUCTS_IBLOCK_ID)
        {
            try
            {
                $recommended = new ProductRecommendation($arFields);
                $recommended->init();
            }
            catch (AniartException $e)
            {
                echo "Exception: \n{$e->getCode()} {$e->getMessage()}\n{$e->__toString()}";
            }
        }
    }

    public function OnBeforeIBlockElementUpdateHandler($arFields)
    {
        define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/logs/deactiv.txt");
        if($arFields['IBLOCK_ID'] == 3 && $arFields['ACTIVE'] == 'N')
        {
            AddMessage2Log('deactiv id = '.print_r($arFields['ID'], true),'');
        }
    }

    public function onAfterIBlockElementAdd($arFields)
    {
        if($arFields['IBLOCK_ID'] == BANNERS_IBLOCK_ID && !empty($arFields['PREVIEW_PICTURE_ID']))
        {
            $stack = app('ImageStackRepository');
            $field = 'PREVIEW_PICTURE';
            $arFile = \CFile::GetFileArray($arFields['PREVIEW_PICTURE_ID']);
            $arData = [
                 'UF_IBLOCK' => $arFields['IBLOCK_ID'],
                 'UF_FILE_ID' => $arFields['PREVIEW_PICTURE_ID'],
                 'UF_FIELD' => $field,
                 'UF_PATH' => $arFile['SRC'],
                 'UF_STATUS' => $stack->getStatusId('need'),
                 'UF_ITEM' => $arFields['ID'],
                 'UF_FILE_NAME' => $arFile['FILE_NAME']
            ];
            $stack->add($arData);
        }
        elseif($arFields['IBLOCK_ID'] == BLOG_IBLOCK_ID)
        {
            if(!empty($arFields['PREVIEW_PICTURE_ID']))
            {
                $arRes['PREVIEW_PICTURE'] = $arFields['PREVIEW_PICTURE_ID'];
            }
            if(!empty($arFields['DETAIL_PICTURE_ID']))
            {
                $arRes['DETAIL_PICTURE'] = $arFields['DETAIL_PICTURE_ID'];
            }

            if(!empty($arRes))
            {
                $stack = app('ImageStackRepository');
                foreach($arRes as $field => $fileId)
                {
                    $arFile = \CFile::GetFileArray($fileId);
                    $arData = [
                         'UF_IBLOCK' => $arFields['IBLOCK_ID'],
                         'UF_FILE_ID' => $arFields['PREVIEW_PICTURE_ID'],
                         'UF_FIELD' => $field,
                         'UF_PATH' => $arFile['SRC'],
                         'UF_STATUS' => $stack->getStatusId('need'),
                         'UF_ITEM' => $arFields['ID'],
                         'UF_FILE_NAME' => $arFile['FILE_NAME']
                    ];
                    $stack->add($arData);
                }

            }
        }
    }
}