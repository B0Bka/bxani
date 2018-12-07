<?php

namespace Aniart\Main\Observers;

use CCatalog;
use CCurrency;
use CCurrencyRates;
use CIBlockElement;
use CIBlockPriceTools;
use CIBlockProperty;
use CModule;
use CPrice;
use Aniart\Main\Repositories\HLBlockRepository;
use CFile;
use CCatalogProduct;
use Aniart\Main\Services\Catalog\CharacterCodeGenerate;
use Aniart\Main\Services\Catalog\ProductActivity;
use Aniart\Main\Exceptions\AniartException;
use Aniart\Main\Tools\Sort;
use Aniart\Main\Orm;
class CatalogObserver
{
    
    const PRODUCTS_IBLOCK = PRODUCTS_IBLOCK_ID;
    const USER_GROUP_DEFAULT = USER_GROUP_DEFAULT;
	const UPDATE_FROM = '08.05.2018';

    public static function onBeforeIBlockElementAdd(&$arFields)
    {
        self::deactiveProduct($arFields);
    }
    
    public static function onBeforeIBlockElementUpdate(&$arFields)
    {
        if($arFields['IBLOCK_ID'] == PRODUCTS_IBLOCK_ID)
        {
            self::generateCharacterCode($arFields);
            $arResSections = self::checkMainSection($arFields);
            if(!empty($arResSections))
            {
                $arFields['IBLOCK_SECTION_ID'] = $arResSections['MAIN_SECTION'];
                $arFields['IBLOCK_SECTION'] = $arResSections['SECTIONS'];
                return $arFields;
            }
            self::checkAstrafitByModel($arFields);
        }
    }
    
    public function onAfterIBlockElementAdd($arg1, $arg2 = false)
    {
        self::updateCatalogPrices($arg1, $arg2);
    }
    
    /**
     * Из-за переноса логики определения активности на сторону сайта, 
     * при срабатывании проверки в checkProductActivity, запускается еще один update.
     * В итоге товар обновляется 2 раза.
     * 
     * @param type $arg1
     * @param type $arg2
     */
    public function onAfterIBlockElementUpdate($arg1, $arg2 = false)
    {
        self::updateCatalogPrices($arg1, $arg2);
        if($arg1['IBLOCK_ID'] == PRODUCTS_IBLOCK_ID && $arg1['ACTIVE'] == 'Y')
            self::sortPreviewPhoto($arg1['ID']);
    }
    
    public function onPriceAdd($arg1, $arg2 = false)
    {
        self::updateCatalogPrices($arg1, $arg2);
    }
    
    public function onPriceUpdate($arg1, $arg2 = false)
    {
        self::updateCatalogPrices($arg1, $arg2);
    }

    public static function deactiveProduct(&$arFields)
    {
        $productsIblockId = self::PRODUCTS_IBLOCK;
        if($productsIblockId == $arFields['IBLOCK_ID'])
        {
            $arFields['ACTIVE'] = 'N';
        }
        return $arFields;
    }
    
    public static function checkProductActivity($product)
    {
        try
        {
            $productActivity = new ProductActivity($product['ID']);
            $productActivity->init();
        }
        catch(AniartException $e)
        {
            echo "Exception: \n{$e->getCode()} {$e->getMessage()}\n{$e->__toString()}";
        }
    }

    public static function generateCharacterCode(&$arFields)
    {
		$productsRepository = app('ProductsRepository');
		$product = $productsRepository->getById($arFields['ID']);
		$dateCreated = $product->getDateCreate();
		$oldCode = $arFields['CODE'];
		if(strtotime($dateCreated) < strtotime(self::UPDATE_FROM))
        {
            try {
                $generate = new CharacterCodeGenerate(
                    $arFields['CODE'],
                    $arFields['ID'],
                    $arFields['PROPERTY_VALUES']
                );
                $code = $generate->getOldCode();
                if (!empty($code)) {
                    $arFields['CODE'] = $code;
                }
                self::updateSiblingCode($arFields['ID'], $arFields['CODE'], $oldCode);
            } catch (AniartException $e) {
                echo "Exception: \n{$e->getCode()} {$e->getMessage()}\n{$e->__toString()}";
            }
            return $arFields;
        }
        else {
            try {
                $generate = new CharacterCodeGenerate(
                    $arFields['CODE'],
                    $arFields['ID'],
                    $arFields['PROPERTY_VALUES']
                );
                $code = $generate->getCode();
                if (!empty($code)) {
                    $arFields['CODE'] = $code;
                }
                self::updateSiblingCode($arFields['ID'], $arFields['CODE'], $oldCode);
            } catch (AniartException $e) {
                echo "Exception: \n{$e->getCode()} {$e->getMessage()}\n{$e->__toString()}";
            }
            return $arFields;
        }
    }

    //Допотопная битриксовая функция (DoIBlockAfterSave)
    public static function updateCatalogPrices($arg1, $arg2 = false)
    {
        $ELEMENT_ID = false;
        $IBLOCK_ID = false;
        $OFFERS_IBLOCK_ID = false;
        $OFFERS_PROPERTY_ID = false;
        
        //product model
        $productModel = true;
        
        if(CModule::IncludeModule('currency'))
            $strDefaultCurrency = CCurrency::GetBaseCurrency();

        //Check for catalog event
        if(is_array($arg2) && $arg2["PRODUCT_ID"] > 0)
        {
            //Get iblock element
            $rsPriceElement = CIBlockElement::GetList(
                array(),
                array(
                    "ID" => $arg2["PRODUCT_ID"],
                ), 
                false, 
                false, 
                array(
                    "ID", 
                    "IBLOCK_ID"
                )
            );
            if($arPriceElement = $rsPriceElement->Fetch())
            {
                $arCatalog = CCatalog::GetByID($arPriceElement["IBLOCK_ID"]);
                if(is_array($arCatalog))
                {
                    //Check if it is offers iblock
                    if($arCatalog["OFFERS"] == "Y")
                    {
                        //Find product element
                        $rsElement = CIBlockElement::GetProperty(
                            $arPriceElement["IBLOCK_ID"], 
                            $arPriceElement["ID"], 
                            "sort", 
                            "asc", 
                            array("ID" => $arCatalog["SKU_PROPERTY_ID"])
                        );
                        $arElement = $rsElement->Fetch();
                        if($arElement && $arElement["VALUE"] > 0)
                        {
                            $ELEMENT_ID = $arElement["VALUE"];
                            $IBLOCK_ID = $arCatalog["PRODUCT_IBLOCK_ID"];
                            $OFFERS_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
                            $OFFERS_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
                        }
                    }
                    //or iblock which has offers
                    elseif($arCatalog["OFFERS_IBLOCK_ID"] > 0)
                    {
                        $ELEMENT_ID = $arPriceElement["ID"];
                        $IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
                        $OFFERS_IBLOCK_ID = $arCatalog["OFFERS_IBLOCK_ID"];
                        $OFFERS_PROPERTY_ID = $arCatalog["OFFERS_PROPERTY_ID"];
                    }
                    //or it's regular catalog
                    else
                    {
                        $ELEMENT_ID = $arPriceElement["ID"];
                        $IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
                        $OFFERS_IBLOCK_ID = false;
                        $OFFERS_PROPERTY_ID = false;
                    }
                }
            }
        }
        //Check for iblock event
        elseif(is_array($arg1) && $arg1["ID"] > 0 && $arg1["IBLOCK_ID"] > 0)
        {
            //Check if iblock has offers
            $arOffers = CIBlockPriceTools::GetOffersIBlock($arg1["IBLOCK_ID"]);
            if(is_array($arOffers))
            {
                $ELEMENT_ID = $arg1["ID"];
                $IBLOCK_ID = $arg1["IBLOCK_ID"];
                $OFFERS_IBLOCK_ID = $arOffers["OFFERS_IBLOCK_ID"];
                $OFFERS_PROPERTY_ID = $arOffers["OFFERS_PROPERTY_ID"];
            }
        }

        if($ELEMENT_ID)
        {
            static $arPropCache = array();
            $arModelProp = [];
            $available = false;
            if(!array_key_exists($IBLOCK_ID, $arPropCache))
            {
                //Check for MINIMAL_PRICE property
                $rsProperty = CIBlockProperty::GetByID("MIN_PRICE", $IBLOCK_ID);
                $arProperty = $rsProperty->Fetch();
                if($arProperty)
                    $arPropCache[$IBLOCK_ID] = $arProperty["ID"];
                else
                    $arPropCache[$IBLOCK_ID] = false;
            }
            if($productModel)
            {
                //Check for MODEL property
                $rsModelProp = CIBlockProperty::GetByID("MODEL", $IBLOCK_ID);
                $arModelProp = $rsModelProp->Fetch();
            }

            $arPropsSet = [];
            $arSizes = []; //Add sizes to product
			$totalQuantitySizes = 0;

            if($arPropCache[$IBLOCK_ID])
            {
                //Compose elements filter
                if($OFFERS_IBLOCK_ID)
                {
                    $rsOffers = CIBlockElement::GetList(
                        array(), 
                        array(
                            "IBLOCK_ID" => $OFFERS_IBLOCK_ID,
                            "PROPERTY_".$OFFERS_PROPERTY_ID => $ELEMENT_ID,
                        ), 
                        false, 
                        false, 
                        array("ID", 'PROPERTY_SIZE', 'PROPERY_MODEL')
                    );
                    while($arOffer = $rsOffers->Fetch())
                    {
                        $arProductID[] = $arOffer['ID'];
                        //Add sizes to product
                        if(empty($arOffer["PROPERTY_SIZE_VALUE"]))
                        {
                            continue;
                        }
                        $productData = CCatalogProduct::GetByID($arOffer['ID']);
                        if($productData['QUANTITY'] <= 0)
                        {
                            continue;
                        }
                        else $available = true;
                        $arSizes[] = $arOffer['PROPERTY_SIZE_VALUE'];
						$totalQuantitySizes += $productData['QUANTITY'];
                    }
                    if(!is_array($arProductID))
                    {
                        $arProductID = array($ELEMENT_ID);
                    }
                }
                else
                {
                    $arProductID = array($ELEMENT_ID);
                }

                $SITE_ID = 's1';
                $minPrice = false;
                $maxPrice = false;
                $optimalPrice = false;
                //Get prices
                $rsPrices = CPrice::GetList(
                    array(), 
                    array(
                        "PRODUCT_ID" => $arProductID,
                        "!CATALOG_GROUP_ID" => PURCHASE_ID_PRICE
                    )
                );
                
                foreach($arProductID as $productId)
                {
                    $optimalReq = CCatalogProduct::GetOptimalPrice(
                        $productId, 
                        1, 
                        array(self::USER_GROUP_DEFAULT), 
                        'N', 
                        array(), 
                        $SITE_ID
                    );

                    if(!empty($optimalReq['DISCOUNT_PRICE']) && ($optimalPrice === false || $optimalPrice > $optimalReq['DISCOUNT_PRICE']))
                        $optimalPrice = $optimalReq['DISCOUNT_PRICE'];
                }

                while($arPrice = $rsPrices->Fetch())
                {
                    if(CModule::IncludeModule('currency') && $strDefaultCurrency != $arPrice['CURRENCY'])
                    {
                        $arPrice["PRICE"] = CCurrencyRates::ConvertCurrency(
                            $arPrice["PRICE"], 
                            $arPrice["CURRENCY"], 
                            $strDefaultCurrency
                        );
                    }

                    $PRICE = $arPrice["PRICE"];

                    if($minPrice === false || $minPrice > $PRICE)
                        $minPrice = $PRICE;

                    if($maxPrice === false || $maxPrice < $PRICE)
                        $maxPrice = $PRICE;
                    
                    if($optimalPrice === false || $optimalPrice > $PRICE)
                        $optimalPrice = $PRICE;
                }

                //Save found minimal price into property
                if($minPrice !== false)
                {
                    $arPropsSet["MIN_PRICE"] = (!empty($optimalPrice) ? $optimalPrice : $minPrice);
                    $arPropsSet["MAX_PRICE"] = $maxPrice;
                    if($arPropsSet["MIN_PRICE"] < $arPropsSet["MAX_PRICE"]) $arPropsSet["SALE"] = SALE_PROP_ENUM_ID;
                }
                
                //Add sizes to product
                if(!empty($arSizes) && count($arSizes) > 0)
                {
                    $arPropsSet["SIZES"] = array_unique($arSizes);
                }
            }

            if($available) $arPropsSet['AVAILABLE'] = AVAILABLE_PROP_ENUM_ID;
			else $arPropsSet['AVAILABLE'] = false;

			//set total quantity
			$arPropsSet['TOTAL_QUANTITY_SIZES'] = $totalQuantitySizes;

            //set property
            if(!empty($arPropsSet))
            {
                CIBlockElement::SetPropertyValuesEx(
                    $ELEMENT_ID, 
                    $IBLOCK_ID, 
                    $arPropsSet
                );
            }
            //add sibling to product
            if(!empty($arModelProp['ID']))
            {
                self::updateSiblings($ELEMENT_ID);
            }
            if(!$available || $arg1['ACTIVE'] == 'N')
                Orm\SortTable::deleteProduct($ELEMENT_ID);
            else
            {
                $sort = new \Aniart\Main\Tools\Sort($ELEMENT_ID);
                $sort->addItem();
            }
        }
    }

    private static function updateSiblingCode($id, $code, $oldCode)
    {
        $rsModelProp = CIBlockProperty::GetByID("MODEL", PRODUCTS_IBLOCK_ID);
        $arModelProp = $rsModelProp->Fetch();
        if(!empty($arModelProp['ID']))
        {
            $rsProduct = CIBlockElement::GetList(
                [],
                array(
                    'IBLOCK_ID' => PRODUCTS_IBLOCK_ID,
                    '=ID' => $id
                ),
                false,
                false,
                ['ID', 'PROPERTY_'.$arModelProp['ID']]
            );
            if($arProduct = $rsProduct->Fetch())
            {
                $productModelProp = $arProduct["PROPERTY_{$arModelProp['ID']}_VALUE"];
            }
            if(!empty($productModelProp))
            {
                $rsProducts = CIBlockElement::GetList(
                    array(),
                    array(
                        'IBLOCK_ID' => PRODUCTS_IBLOCK_ID,
                        'ACTIVE' => 'Y',
                        '!=ID' => $id,
                        'PROPERTY_'.$arModelProp['CODE'] => $productModelProp,
                    ),
                    false,
                    false,
                    array('ID', 'DETAIL_PAGE_URL', 'PROPERTY_SIBLINGS', 'CODE')
                );
                while($arSibling = $rsProducts->GetNext())
                {
                    $newSibling = html_entity_decode(str_replace($oldCode, $code, $arSibling['PROPERTY_SIBLINGS_VALUE']));
                    $res = CIBlockElement::SetPropertyValuesEx($arSibling['ID'], PRODUCTS_IBLOCK_ID, ['SIBLINGS' => $newSibling]);
                }
            }
        }
    }

    private static function updateSiblings($id)
    {
        $countSiblings = 0;
        $limitSiblings = 14;
        $rsModelProp = CIBlockProperty::GetByID("MODEL", PRODUCTS_IBLOCK_ID);
        $arModelProp = $rsModelProp->Fetch();
        if(!empty($arModelProp['ID']))
        {
            $rsProduct = CIBlockElement::GetList(
                [],
                array(
                    'IBLOCK_ID' => PRODUCTS_IBLOCK_ID,
                    '=ID' => $id
                ),
                false,
                false,
                ['ID', 'PROPERTY_'.$arModelProp['ID']]
            );
            if($arProduct = $rsProduct->Fetch())
            {
                $productModelProp = $arProduct["PROPERTY_{$arModelProp['ID']}_VALUE"];
            }
            if(!empty($productModelProp))
            {

                $rsProducts = CIBlockElement::GetList(
                    array(),
                    array(
                        'IBLOCK_ID' => PRODUCTS_IBLOCK_ID,
                        'ACTIVE' => 'Y',
                        'PROPERTY_'.$arModelProp['CODE'] => $productModelProp,
                        '!PROPERTY_AVAILABLE' => false
                    ),
                    false,
                    false,
                    array('ID', 'DETAIL_PAGE_URL', 'PROPERTY_SIBLINGS', 'CODE', 'PROPERTY_CLOTH', 'PROPERTY_MORE_PHOTO','PROPERTY_MORE_PHOTO_PREVIEW', 'PROPERTY_MORE_PHOTO_ELITE')
                );
                while($arSibling = $rsProducts->GetNext())
                {
                    $arPic = [];
                    $countSiblings++;
                    if($countSiblings >= $limitSiblings)
                    {
                        break;
                    }

                    $cloth[$arSibling['ID']] = $arSibling['PROPERTY_CLOTH_VALUE'];

                    if(!empty($arSibling['PROPERTY_MORE_PHOTO_PREVIEW_VALUE']))
                        $arPhoto = self::getPictures($arSibling['PROPERTY_MORE_PHOTO_PREVIEW_VALUE']);
                    else
                        $arPhoto = self::getPictures($arSibling['PROPERTY_MORE_PHOTO_VALUE']);
                    $arElite = self::getPictures($arSibling['PROPERTY_MORE_PHOTO_ELITE_VALUE']);

                    $arSiblings[$arSibling['ID']] = [
                        'id' => $arSibling['ID'],
                        'url' => $arSibling['DETAIL_PAGE_URL'],
                        'photo' => $arPhoto,
                        'elite' => $arElite
                    ];
                }
                $arCloth = self::getCloth($cloth);

                foreach($arSiblings as $key => $sibling)
                {
                    $siblingColor = $arCloth[$cloth[$key]];
                    $arSiblings[$key]['img'] = (empty($siblingColor) ? '' : CFile::GetPath($siblingColor));
                }

                foreach($arSiblings as $key => $sibling)
                {
                    $arTmp = $arSiblings;
                    unset($arTmp[$key]);
                    $str = json_encode($arTmp);
                    $res = CIBlockElement::SetPropertyValuesEx($key, PRODUCTS_IBLOCK_ID, ['SIBLINGS' => $str]);
                }
            }
        }
    }

    private static function getPictures($arId, $count = 2)
    {
        for($i = 0; $i<$count ; $i++)
        {
            $res[] = \CFile::getPath($arId[$i]);
        }
        return $res;
    }

    private static function getCloth($arData)
    {
        $result = [];
        $repository = new HLBlockRepository(HL_CLOTH_ID);
        $cloth = $repository->getList(
            ['UF_SORT' => 'ASC'], 
            ['UF_XML_ID' => $arData]
        );
        if(empty($cloth))
        {
            return $result;
        }
        foreach($cloth as $item)
        {
            $fields = $item->getFields();
            $file = $fields['UF_FILE'];
            if(empty($file))
            {
                continue;
            }
            $result[$fields['UF_XML_ID']] = $file;
        }
        return $result;
    }
    
    public static function checkMainSection($arData)
    {
        if(empty($arData['IBLOCK_SECTION']) || empty($arData['IBLOCK_SECTION_ID']))
            return false;
        $sectionsRepository = app('ProductSectionsRepository');
		$sections = $sectionsRepository->getList(['ID' => 'ASC'], ['ID' => $arData['IBLOCK_SECTION']]);
		foreach($sections as $section)
        {
            $sectionId = $section->getId();
            if($section->getMainSection() == true && $sectionId != $arData['IBLOCK_SECTION_ID'])
            {
                $res['MAIN_SECTION'] = $sectionId;
                $res['SECTIONS'] = $arData['IBLOCK_SECTION'];
                return $res;
            }
        }
    }

    public static function checkAstrafitByModel($arFields)
    {
        $db_enum_list = \CIBlockProperty::GetPropertyEnum("ASRTAFIT_BY_MODEL", Array(), Array("IBLOCK_ID"=>PRODUCTS_IBLOCK_ID));
        while($ar_enum_list = $db_enum_list->GetNext())
        {
            $propId = $ar_enum_list['PROPERTY_ID'];
            if($ar_enum_list['XML_ID'] == 'y')
                $yesPropId = $ar_enum_list['ID'];
        }
        foreach($arFields['PROPERTY_VALUES'][$propId] as $val)
        {
            if($val['VALUE'] == $yesPropId)
                self::setAstrafitByModel($arFields['ID'], $yesPropId);
        }
    }

    public static function setAstrafitByModel($productId, $yesPropId)
    {
        $rsModelProp = CIBlockProperty::GetByID("MODEL", PRODUCTS_IBLOCK_ID);
        $arModelProp = $rsModelProp->Fetch();
        if(!empty($arModelProp['ID']))
        {
            $rsProduct = CIBlockElement::GetList(
                [],
                array(
                    'IBLOCK_ID' => PRODUCTS_IBLOCK_ID,
                    '=ID' => $productId
                ),
                false,
                false,
                ['ID', 'PROPERTY_'.$arModelProp['ID']]
            );
            if($arProduct = $rsProduct->Fetch())
            {
                $productModelProp = $arProduct["PROPERTY_{$arModelProp['ID']}_VALUE"];
            }
            if(!empty($productModelProp))
            {

                $rsProducts = CIBlockElement::GetList(
                    array(),
                    array(
                        'IBLOCK_ID' => PRODUCTS_IBLOCK_ID,
                        'PROPERTY_'.$arModelProp['CODE'] => $productModelProp,
                        '!ID' => $productId,
                        'PROPERTY_ASRTAFIT_BY_MODEL' => false
                    ),
                    false,
                    false,
                    array('ID')
                );
                while($arModelItem = $rsProducts->GetNext())
                {
                    CIBlockElement::SetPropertyValuesEx($arModelItem['ID'], PRODUCTS_IBLOCK_ID, ['ASRTAFIT_BY_MODEL' => $yesPropId]);
                }
            }
        }
    }

    private static function sortPreviewPhoto($id)
    {
        $stack = app('ImageStackRepository');
        $db_props = CIBlockElement::GetProperty(PRODUCTS_IBLOCK_ID, $id, array("sort" => "asc"), Array("CODE"=>"MORE_PHOTO"));
        while ($ob = $db_props->GetNext())
        {
            if(!empty($ob['VALUE']))
                $arOriginal[] = $ob['VALUE'];
        }
        if(empty($arOriginal))
            return false;
        $db_props = CIBlockElement::GetProperty(PRODUCTS_IBLOCK_ID, $id, array("sort" => "asc"), Array("CODE"=>"MORE_PHOTO_PREVIEW"));
        while ($ob = $db_props->GetNext())
        {
            $arPreview[] = $ob['VALUE'];
        }

        for($i = 0; $i<=1; $i++)
        {
            $originFile = \CFile::makeFileArray($arOriginal[$i]);

            if($stack->imageExists($id, $originFile['name']))
                continue;

            $prevFile = \CFile::makeFileArray($arPreview[$i]);
            if($originFile['name'] != $prevFile['name'] || empty($arPreview[$i]))
                $diff = true;

            $bxresized = \CFile::ResizeImageGet($arOriginal[$i], ['width' => 282, 'height' => 424], BX_RESIZE_IMAGE_PROPORTIONAL, true);
            $arData[] = [
                 'UF_IBLOCK' => PRODUCTS_IBLOCK_ID,
                 'UF_FIELD' => 'PROPERTY_MORE_PHOTO_PREVIEW',
                 'UF_PATH' => $bxresized['src'],
                 'UF_STATUS' => $stack->getStatusId('need'),
                 'UF_ITEM' => $id,
                 'UF_FILE_NAME' => $originFile['name'],
                 'UF_ORIGINAL_NAME' => $originFile['name'],
            ];
        }
        if($diff)
        {
            foreach($arData as $data)
                $stack->add($data);

            return true;
        }

        return false;
    }
}
