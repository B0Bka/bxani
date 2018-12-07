<?php


namespace Aniart\Main\Observers;


class BitrixObserver
{
    public static function onProlog()
    {
        $redirect = new \Aniart\Main\Seo\SeoRedirects();
        $redirect->init();
    }

    public static function onEpilog()
    {
        self::setSeoParams();
    }

    protected static function setSeoParams()
    {
        if (defined('ERROR_404') && ERROR_404 == 'Y') {
            seo()->setPageTitle('404 Not Found', true);
            seo()->setMetaTitle('404 Not Found', true);
        }
        seo()->process();
    }

    function OrderDetailAdminContextMenuShow(&$items)
    {
        if (($_SERVER['REQUEST_METHOD'] == 'GET' && $GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/iblock_element_edit.php'
                && $_REQUEST['ID'] > 0 && empty($_REQUEST['action'])) && ($_REQUEST['IBLOCK_ID'] == 11 || $_REQUEST['IBLOCK_ID'] == 2)) {
            if ($_REQUEST['IBLOCK_ID'] == 11) $class = 'set-bt';
            else $class = 'buy-bt';
            $items[] = array("TEXT" => "Ссылка на кнопку покупки", "LINK" => "javascript:getBuyUrl('" . $_REQUEST['ID'] . "', '" . $class . "');");

            \CJSCore::RegisterExt('adminBt', array(
                'js' => '/local/templates/main/js/src/module/admin.js',
            ));
            \CJSCore::Init(array('jquery', 'adminBt', 'popup'));

        }
    }

    function OnAfterUserUpdateHandler(&$arFields)
    {
        define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/local/logs/userupdate.txt");
        AddMessage2Log($arFields, "comment");
    }

    function OnBeforeUserRegisterHandler(&$arFields)
    {
        define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/local/logs/userregister.txt");
        AddMessage2Log($arFields, "register");
    }

    public function onFileSave(
        &$arFile,
        $strFileName,
        $strSavePath
    )
    {
        if ($strSavePath != "iblock") {
            return false;
        }

        try {
            $trace = debug_backtrace();

            $iblockId = 0;
            $itemId = 0;
            foreach ($trace as $traceItem) {
                if ($traceItem["function"] == "SetPropertyValues" && $traceItem["class"] == "CIBlockElement") {
                    if ($traceItem["args"][1] > 0 && $traceItem["args"][0] > 0) {
                        $iblockId = intval($traceItem["args"][1]);
                        $itemId = intval($traceItem["args"][0]);
                        $field = self::getImageField($iblockId, $traceItem["args"][2], $arFile['ORIGINAL_NAME']);
                        break;
                    } else {
                        return false;
                    }
                }

                if ($traceItem["function"] == "SetPropertyValuesEx" && $traceItem["class"] == "CAllIBlockElement") {
                    $itemId = intval($traceItem["args"][0]);
                    if ($itemId <= 0) {
                        return false;
                    }

                    $iblockId = intval($traceItem["args"][1]);
                    $field = self::getImageField($iblockId, $traceItem["args"][2], $arFile['ORIGINAL_NAME']);
                    if ($iblockId <= 0 && Loader::includeModule("iblock")) {
                        $row = Iblock\ElementTable::getRow([
                            "filter" => [
                                "=ID" => $itemId
                            ],
                            "select" => [
                                "IBLOCK_ID"
                            ]
                        ]);
                        if ($row && $row["IBLOCK_ID"]) {
                            $iblockId = intval($row["IBLOCK_ID"]);
                        }
                    }

                    if ($iblockId > 0 && $itemId > 0) {
                        break;
                    } else {
                        return false;
                    }
                }

                if ($traceItem["function"] == "SaveForDB" && $traceItem["class"] == "CAllFile") {
                    if ($traceItem["args"][0]["IBLOCK_ID"] > 0) {
                        $iblockId = intval($traceItem["args"][0]["IBLOCK_ID"]);
                        $itemId = intval($traceItem["args"][0]["XML_ID"]);
                        $field = self::getImageField($iblockId, $traceItem["args"][0], $arFile['ORIGINAL_NAME']);
                        break;
                    }
                }
            }

            if ($iblockId > 0 && $itemId > 0 && !empty($field)) {
                $stack = app('ImageStackRepository');
                $strDirName = '/upload/tmp_image/'.$iblockId.'/'.$itemId.'/'.$field.'/';
                CheckDirPath($_SERVER['DOCUMENT_ROOT'].$strDirName);
                copy($arFile["tmp_name"], $_SERVER['DOCUMENT_ROOT'].$strDirName.$strFileName);
                $arData = [
                     'UF_IBLOCK' => $iblockId,
                     'UF_FIELD' => $field,
                     'UF_PATH' => $strDirName.$strFileName,
                     'UF_STATUS' => $stack->getStatusId('need'),
                     'UF_ITEM' => $itemId,
                     'UF_FILE_NAME' => $strFileName,
                     'UF_ORIGINAL_NAME' => $arFile['ORIGINAL_NAME']
                ];
                 \Bitrix\Main\Diag\Debug::writeToFile($arData, "22222", "/local/logs/file.txt");
                $stack->add($arData);
            }

        } catch (\Exception $e) {

        }
        return false;

    }

    private function getImageField($iblockId, $arData, $fileName)
    {
        if($iblockId == PRODUCTS_IBLOCK_ID)
        {
            foreach($arData[66] as $elite)
            {
                if($elite['VALUE']['name'] == $fileName)
                    return 'PROPERTY_MORE_PHOTO_ELITE';
            }
            foreach($arData[7] as $photo)
            {
                if($photo['VALUE']['name'] == $fileName)
                    return 'PROPERTY_MORE_PHOTO';
            }
        }
        elseif($iblockId == BANNERS_IBLOCK_ID && $arData['PREVIEW_PICTURE']['name'] == $fileName)
            return 'PREVIEW_PICTURE';
        elseif($iblockId == BLOG_IBLOCK_ID)
        {
            if($arData['PREVIEW_PICTURE']['name'] == $fileName)
                return 'PREVIEW_PICTURE';
            elseif($arData['DETAIL_PICTURE']['name'] == $fileName)
                return 'DETAIL_PICTURE';
        }
        elseif($iblockId == MAIN_SLIDER_IBLOCK_ID)
        {
            foreach($arData[71] as $photo)
            {
                if($photo['VALUE']['name'] == $fileName)
                    return 'PROPERTY_PICTURE_XL';
            }
            foreach($arData[72] as $photo)
            {
                if($photo['VALUE']['name'] == $fileName)
                    return 'PROPERTY_PICTURE_L';
            }
            foreach($arData[73] as $photo)
            {
                if($photo['VALUE']['name'] == $fileName)
                    return 'PROPERTY_PICTURE_M';
            }
            foreach($arData[74] as $photo)
            {
                if($photo['VALUE']['name'] == $fileName)
                    return 'PROPERTY_PICTURE_S';
            }
            foreach($arData[75] as $photo)
            {
                if($photo['VALUE']['name'] == $fileName)
                    return 'PROPERTY_PICTURE_XS';
            }
        }
    }
}