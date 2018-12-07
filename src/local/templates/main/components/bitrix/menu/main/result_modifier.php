<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$sections = [];
$imgSection = [];
$i = 0;

foreach($arResult as $val)
{
    $banners = [];
    if(strpos($val["TEXT"], "img-") !== false){
        $sections[] = [
            'ID' => $val['PARAMS']['ID'],
            'NAME' => $val['TEXT'],
            'LINK' => $val['LINK'],
            'IBLOCK_SECTION_ID' => $val['PARAMS']['IBLOCK_SECTION_ID'],
            'DEPTH_LEVEL' => $val['PARAMS']['DEPTH_LEVEL'],
            'ICON' => "TRUE"
        ];
    } else {
        if($val['PARAMS']['BANNER_ID'] > 0)
        {
            $arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_LINK");
            $arFilter = Array("IBLOCK_ID"=>9, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y", "SECTION_ID" => $val['PARAMS']['BANNER_ID']);
            $res = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, Array("nPageSize"=>3), $arSelect);
            while($ob = $res->GetNextElement())
            {

             $arFields = $ob->GetFields();
             $banner = ['NAME' => $arFields['NAME'], 'LINK' => $arFields['PROPERTY_LINK_VALUE'], 'PICTURE' => CFile::GetPath($arFields["PREVIEW_PICTURE"]) ];
             $banners[] = $banner;
            }

        }
        $sections[] = [
            'ID' => $val['PARAMS']['ID'],
            'NAME' => $val['TEXT'],
            'LINK' => $val['LINK'],
            'IBLOCK_SECTION_ID' => $val['PARAMS']['IBLOCK_SECTION_ID'],
            'DEPTH_LEVEL' => $val['PARAMS']['DEPTH_LEVEL'],
            'ICON' => $val['PARAMS']['ICON'],
            'BANNERS' => $val['PARAMS']['BANNER_ID']
        ];
    }
}

$arResult = buildTree($sections, 'IBLOCK_SECTION_ID', 'ID');

foreach($arResult as $val){?>
    <?if(strpos($val["NAME"], "img-") !== false){
        $imgVal = explode("-", $val["NAME"]);
        $imgLink = explode(";", $val["LINK"]);
        $imgSection[$i]["NAME"] = $imgVal[1];
        $imgSection[$i]["LINK"] = $imgLink[0];
        $imgSection[$i]["HREF"] = $imgLink[1];
        $i++;
        unset($val);
    }
    $arResult["imgSection"] = $imgSection
    ?>
<?}?>