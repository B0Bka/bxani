<?php
namespace Aniart\Main\Observers;
/**
 * Search index
 */
class SearchObserver
{
    function BeforeIndexHandler($arFields)
    {
        if($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == PRODUCTS_IBLOCK_ID)
        {
            if(array_key_exists("BODY", $arFields) && !stristr($arFields["ITEM_ID"], 's'))
            {
                $productRepositoryInstance = app("ProductsRepository");
                $filter = ["ID" => $arFields["ITEM_ID"]];
                $arProduct = $productRepositoryInstance->getList([], $filter);
                $product = array_shift($arProduct);
                $arFields["BODY"] = $product->getArticle();
                if($name = $product->getPropertyValue('NAME_SHOW')) $arFields["BODY"] .= ' '.$name;
            }
        }
        return $arFields;
    }
}