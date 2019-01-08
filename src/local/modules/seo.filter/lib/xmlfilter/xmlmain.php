<?
namespace Seo\Filter\XmlFilter;
use Seo\Filter\XmlFilter\XmlGenerator;
class XmlMain
{
    private $iblockId;
    private $arItems;

    public function __construct($iblockId)
    {
        $this->iblockId = $iblockId;
    }

    public function init()
    {
        $this->getFilters();
        if(!empty($this->arItems))
        {
            $this->makeXml();
        }
    }

    private function getFilters()
    {
        $sections = $this->getSections();
        foreach($sections as $section)
        {
            $obSectionFilter = new \Seo\Filter\XmlFilter\XmlSection($section,  $this->iblockId);
            $sectionFilter = $obSectionFilter->genSectionFilterSitemap();
            if(!empty($sectionFilter['ITEMS']))
            {
                $this->addItems($sectionFilter['ITEMS']);
            }
        }
    }

    private function getSections()
    {
        $arFilter = ['IBLOCK_ID'=>$this->iblockId, 'GLOBAL_ACTIVE'=>'Y', 'ACTIVE' => 'Y'];
        $dbList = \CIBlockSection::GetList(['ID' => 'asc'], $arFilter, true, ['ID']);
        while($arResult = $dbList->GetNext())
        {
            $res[] = $arResult['ID'];
        }

        return $res;
    }

    private function addItems($data)
    {
        if(!empty($this->arItems))
            array_merge($this->arItems, $data);
        else
            $this->arItems = $data;

        return true;
    }

    private function makeXml()
    {
        $generator = new XmlGenerator('/filter.xml', $this->arItems);
        $generator->init();
    }
}
?>