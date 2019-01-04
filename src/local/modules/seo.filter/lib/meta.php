<?php
namespace Seo\Filter;

use Bitrix\Iblock\Template;

class Meta
{
    private $sectionId;
    private $filterStr = '{=filter}';
    private $params;
    private $iblockId;
    private $meta;

    public function __construct($iblockId, $params, $sectionId)
    {
        $this->iblockId = $iblockId;
        $this->sectionId = $sectionId;
        $this->params = $params;
        $this->meta = $this->getMeta();
    }

    private function getMeta()
    {
        $arTemplate = $this->getMetaTemplate();
        $filter = $this->getFilterStr();
        foreach ($arTemplate as $template)
        {
            if(substr_count($template['TEMPLATE'], $this->filterStr) > 0)
            {
                $tpl = str_replace($this->filterStr, $filter, $template['TEMPLATE']);
                $entity = new Template\Entity\Section($this->sectionId);
                $arTpl[$template['CODE']] = \Bitrix\Iblock\Template\Engine::process($entity, $tpl);
            }
        }

        return $arTpl;
    }

    public function setCanonical()
    {
        global $APPLICATION;
        $link = $this->getProtocol().$_SERVER['HTTP_HOST'] . $APPLICATION->GetCurPage(false);
        \Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="canonical" href="'.$link.'" />');
    }

    public function setMeta()
    {
        global $APPLICATION;
        foreach ($this->meta as $key => $meta)
        {
            switch ($key)
            {
                case 'SECTION_META_TITLE':
                    $APPLICATION->SetPageProperty('title', $meta);
                    break;
                case 'SECTION_META_DESCRIPTION':
                    $APPLICATION->SetPageProperty('description', $meta);
                    break;
            }
        }
    }

    public function getH1()
    {
        return $this->meta['SECTION_PAGE_TITLE'];
    }


    private function getMetaTemplate()
    {
        $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($this->iblockId , $this->sectionId);
        return $ipropValues->queryValues();
    }

    private function getFilterStr()
    {
        foreach ($this->params as $item)
        {
            foreach ($item['VALUES'] as $val)
            {
                if($val['CHECKED'])
                {
                    $arChecked[] = $val['VALUE'];
                }
            }
        }
        return implode(', ', $arChecked);
    }

    private function getProtocol()
    {
        return stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
    }
}