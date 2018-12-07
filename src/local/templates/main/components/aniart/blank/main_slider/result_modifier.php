<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();

$by = 's_weight';
$order = 'asc';

$banners = \CAdvBanner::GetList(
    $by, 
    $order, 
    [
        'ACTIVE' => 'Y', 
        'LAMP' => 'green', 
        'TYPE_SID' => $arParams['IBLOCK_ID'], 
        'TYPE_SID_EXACT_MATCH' => 'Y'
    ], 
    $is_filtered, 
    'N'
);
while($row = $banners->Fetch())
{
    
    $row['img'] = CFile::GetPath($row['IMAGE_ID']);
    $row['html'] = \CAdvBanner::GetHTML($row);
    $arResult[] = $row;
    //dBug($row);
}
//die;
?>