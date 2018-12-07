<?php


namespace Aniart\Main\Models;


use Aniart\Main\Multilang\Models\IblockSectionModelML;

class ProductSection extends IblockSectionModelML
{
    public function getPicture($id)
    {
        $picture = [
            'src' => '',
            'height' => '',
            'width' => '',
            'alt' => ''
        ];
        if(empty($id))
        {
            return $picture;
        }
        $fileData = \CFile::GetFileArray($id);
        if(!empty($fileData)){
            $picture = [
                'src' => $fileData['SRC'],
                'width' => $fileData['WIDTH'],
                'height' => $fileData['HEIGHT'],
                'alt' => $picture['alt']
            ];
        }
        return $picture;
    }
    /*
     * Возвращает ид раздела по класификации гугла,
     * если пусто, то возвращает ид Apparel & Accessories	Clothing
     */
    public function getGoogleId()
    {
        $val = $this->getPropertyValue('GOOGLE_ID');
        return !empty($val) ? $val : '1604';
    }

    public function getLamodaTitle()
    {
        return $this->getPropertyListValue('LAMODA_TITLE');
    }

    public function getLamodaSubset()
    {
        return $this->getPropertyListValue('SUBSET');
    }

    public function getMainSection()
    {
        return $this->getPropertyValue('MAIN');
    }

    public function checkNoindex()
    {
        if(!empty($this->getPropertyValue('NOINDEX')))
        {
            global $APPLICATION;
            $APPLICATION->AddHeadString('<meta name="robots" content="noindex, follow" />', true);
            return true;
        }
        return false;
    }

    public function getNotShowSizeType()//не выводить смену размеров для товаров раздела
    {
        return $this->getPropertyValue('NOT_SHOW_SIZES');
    }

}