<?

namespace Aniart\Main\Models;

class Slider extends IblockElementModel
{
    public function __construct(array $fields = [])
    {
        parent::__construct($fields);
    }
    
    public function getPictureXL()
    {
        return $this->getFilePath($this->getPropertyValue('PICTURE_XL'));
    }
    
    public function getPictureL()
    {
        return $this->getFilePath($this->getPropertyValue('PICTURE_L'));
    }
    
    public function getPictureM()
    {
        return $this->getFilePath($this->getPropertyValue('PICTURE_M'));
    }
    
    public function getPictureS()
    {
        return $this->getFilePath($this->getPropertyValue('PICTURE_S'));
    }
    
    public function getPictureXS()
    {
        return $this->getFilePath($this->getPropertyValue('PICTURE_XS'));
    }
    
    public function getAreaXL()
    {
        return $this->getPropHtml($this->getPropertyValue('AREA_XL'));
    }
    
    public function getAreaL()
    {
        return $this->getPropHtml($this->getPropertyValue('AREA_L'));
    }
    
    public function getAreaM()
    {
        return $this->getPropHtml($this->getPropertyValue('AREA_M'));
    }
    
    public function getAreaS()
    {
        return $this->getPropHtml($this->getPropertyValue('AREA_S'));
    }
    
    public function getAreaXS()
    {
        return $this->getPropHtml($this->getPropertyValue('AREA_XS'));
    }
    
    public function getPropHtml($data)
    {
        if(empty($data['TEXT']))
        {
            return '';
        }
        return htmlspecialcharsBack($data['TEXT']);
    }

    public function getLink()
    {
        return $this->getPropertyValue('LINK');
    }
}
