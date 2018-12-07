<?
namespace Aniart\Main\Models;

class Promotion extends IblockElementModel
{
    public function __construct(array $fields = [])
    {
        parent::__construct($fields);
    }

    public function getAplication()
    {
        global $APPLICATION;
        return $APPLICATION;
    }
    
    public function getPicture()
    {
        $id = parent::getPreviewPictureId();
        return \CFile::GetPath($id);
    }

    public function getDate()
    {
        $date = parent::getDateActiveFrom();
        return ConvertDateTime($date, "DD.MM.YYYY", "ru");
    }

    public function getItems()
    {
        return parent::getPropertyValue('ITEMS');
    }

    public function getDetailPicture()
    {
        $id = parent::getDetailPictureId();
        return \CFile::GetPath($id);
    }

    public function getType()
    {
        return parent::getPropertyValueXmlId('TYPE');
    }

    public function getLink()
    {
        return parent::getPropertyValue('LINK');
    }
}
?>