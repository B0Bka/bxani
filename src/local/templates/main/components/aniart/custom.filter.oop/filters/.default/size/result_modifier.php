<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$property = $arParams["PROPERTY"];
$sectionRepository = app('ProductSectionsRepository');
if($arParams['SECTION_ID'] > 0)
{
    $section = $sectionRepository->getById($arParams['SECTION_ID']);
    if(!$section->getNotShowSizeType())
    {
        $cnt = $property->ValuesCount();
        if ($cnt > 0)
        {
            foreach ($property->GetValues() as $value)
            {
                $arSizes[] = $value['ID'];
            }
            $sizeRepository = app('SizesRepository');
            $arParams['SIZES'] = $sizeRepository->getSizes();
            $sizes = $sizeRepository->getList([],['UF_XML_ID' => $arSizes]);
            foreach ($sizes as $size)
            {
                $arParams['NAMES'][$size->getXmlId()]['ua'] = $size->getNameUa();
                $arParams['NAMES'][$size->getXmlId()]['int'] = $size->getNameInt();
            }
        }
    }
}
