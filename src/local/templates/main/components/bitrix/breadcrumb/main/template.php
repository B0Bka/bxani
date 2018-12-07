<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult)) return "";

$strReturn = '';

$strReturn .= '<div class="broad"><ul itemscope itemtype="http://schema.org/BreadcrumbList">';

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
    $position = $index + 1;
    $title = htmlspecialcharsex($arResult[$index]['TITLE']);
    $arrow = '<i class="fa fa-angle-right" aria-hidden="true"></i>';

    if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1) {
        $strReturn .= '
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemprop="item" href="'.ACTUAL_LINK.$arResult[$index]["LINK"].'" title="'.$title.'" ><span itemprop="name">'.$title.'</span></a>
            <meta itemprop="position" content="'.$position.'" />
            '.$arrow.'
            </li>';
    }
    else
    {
        $strReturn .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">'.$title.'</span>
<meta itemprop="position" content="' . $position . '" />
</li>';
    }
}

$strReturn .= '</ul></div>';

return $strReturn;
