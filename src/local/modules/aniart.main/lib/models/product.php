<?
namespace Aniart\Main\Models;
use Aniart\Main\Interfaces\DiscountPricebleInterface,
    Aniart\Main\Traits\DiscountPricebleTrait,
    Aniart\Main\Traits\ProductPropCodeTrait,
    Aniart\Main\FavoritesTable,
    Aniart\Main\Repositories\FavoritesRepository;

class Product extends IblockElementModel implements DiscountPricebleInterface
{

    use DiscountPricebleTrait,
        ProductPropCodeTrait;

    const TEMPLATE_PATH = SITE_TEMPLATE_PATH;

    /**
     * @var \Aniart\Main\Repositories\OffersRepository
     */
    private $offerRepositoryInstance;

    /**
     * @var \Aniart\Main\Repositories\TypesRepository
     */
    private $typesRepositoryInstance;

    /**
     * @var \Aniart\Main\Repositories\TagsRepository
     */
    private $tagsRepositoryInstance;
    private $collectionsRepositoryInstance;
    private $type;
    private $collection;
    private $price;
    private $maxprice;
    private $basePrice;
    private $offers;
    private $sibling;
    private $elite = false;

    public function __construct(array $fields = [])
    {
        $this->offerRepositoryInstance = app("OffersRepository");
        $this->typesRepositoryInstance = app("TypesRepository");
        $this->tagsRepositoryInstance = app("TagsRepository");
        parent::__construct($fields);
    }

    /**
     * @param Offer[] $offers
     * @return $this
     */
    public function setOffers(array $offers = [])
    {
        $this->offers = [
            'all' => [],
            'minPrice' => false
        ];
        if(!empty($offers))
        {
            $offer = current($offers);
            $offer = array_reduce($offers, function(Offer $offer, Offer $o)
            {
                if(($o->getPrice() < $offer->getPrice()))
                {
                    $offer = $o;
                }
                return $offer;
            }, $offer);

            $this->offers['all'] = $offers;
            $this->offers['minPrice'] = $offer;
        }
        return $this;
    }

    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    public function setSibling($sibling)
    {
        $this->sibling = $sibling;
        return $this;
    }

    protected function createSection(array $fields = [])
    {
        return app('ProductSectionsRepository')->newInstance($fields);
    }

    public function getAplication()
    {
        global $APPLICATION;
        return $APPLICATION;
    }

    public function getName()
    {
        $name = parent::getName();
        $nameShow = $this->getPropertyValue($this->getPropCode('name_show'));
        if(empty($nameShow))
        {
            return $name;
        }
        return $nameShow;
    }

    public function getUrl()
    {
        return parent::getDetailPageUrl();
    }

    public function getMinPicture($id, $width = 60, $height = 90)
    {
        return $this->getPictureInfo($id, $width, $height);
    }

    private function getPictureInfo($pictureId, $width, $height)
    {
        $pictureId = (int)$pictureId;
        if(!$pictureId)
        {
            return false;
        }
        $images = getResizedImages(
            [$pictureId],
            ['picture' => ['width' => $width, 'height' => $height]]
        );
        return $images[$pictureId]['picture'];
    }

    public function getAllImagesId($count = false)
    {
        $imagesID = [];
        if($this->getPropertyValue('MORE_PHOTO'))
        {
            $imagesID = array_merge($imagesID, (array) $this->getPropertyValue('MORE_PHOTO'));
        }
        if($count)
        {
            $imagesID = array_slice($imagesID, 0, $count);
        }

        return $imagesID;
    }

    public function getOptimizedImagesId($count = false)
    {
        $imagesID = [];
        if($this->getPropertyValue('MORE_PHOTO_OPTIMIZED'))
        {
            $imagesID = array_merge($imagesID, (array) $this->getPropertyValue('MORE_PHOTO_OPTIMIZED'));
        }
        elseif($this->getPropertyValue('MORE_PHOTO'))
        {
            $imagesID = array_merge($imagesID, (array) $this->getPropertyValue('MORE_PHOTO'));
        }
        if($count)
        {
            $imagesID = array_slice($imagesID, 0, $count);
        }

        return $imagesID;
    }

    public function getImages($data, $count = false, $width, $height)
    {
        $result = [];
        $images = [];
        if(!empty($data))
        {
            $images = array_merge($images, (array) $data);
        }
        if($count)
        {
            $images = array_slice($images, 0, $count);
        }
        if(empty($images))
        {
            $result[] = $this->getImageStub($width, $height);
        }
        else
        {
            foreach($images as $image)
            {
                $result[] = $this->getMinPicture($image, $width, $height);
            }
        }
        return $result;
    }

    public function getMorePhoto($count = false)
    {
        $this->elite = false;
        $data = $this->getPropertyValue($this->getPropCode('more_photo'));
        return $this->getImages($data, $count, 282, 424);
    }

    public function getMorePhotoPreview()
    {
        $result = [];
        $this->elite = false;
        $data = $this->getPropertyValue($this->getPropCode('more_photo_preview'));
        if(!empty($data))
        foreach($data as $photo)
            $result[] = ['src' => \CFile::GetPath($photo)];
        else $result = $this->getMorePhoto(2);
        return $result;
    }

    public function getMorePhotoElitePreview()
    {
        $result = [];
        $this->elite = true;
        $data = $this->getPropertyValue($this->getPropCode('more_photo_elite_preview'));
        if(!empty($data))
        foreach($data as $photo)
            $result[] = ['src' => \CFile::GetPath($photo)];
        else $result = $this->getMorePhotoElite(2);
        return $result;
    }

    public function getFullPhoto($count = false)
    {
        $result = [];
        $picsID = $this->getAllImagesId();
        foreach($picsID as $id)  $result[] = \CFile::GetPath($id);
        return $result;
    }

    public function getOptimizedPhoto()
    {
        $result = [];
        $picsID = $this->getOptimizedImagesId();
        foreach($picsID as $id)  $result[] = \CFile::GetPath($id);
        return $result;
    }

    public function getMorePhotoElite($count = false)
    {
        $data = $this->getPropertyValue($this->getPropCode('more_photo_elite'));
        return $this->getImages($data, $count,  590, 424);
    }

    public function getImageStub($width, $height)
    {
        $templatePath = self::TEMPLATE_PATH;
        $image = ($this->elite ? 'no_photo_2.png' : 'no_photo_1.png');
        return [
            'src' => "{$templatePath}/images/{$image}",
            'width' => $width,
            'height' => $height
        ];
    }

    public function getFirstLevelSection()
    {
        $section = array_shift($this->getSections());
        return $section->IBLOCK_SECTION_ID ?: $section->getId();
    }

    public function getBasePrice($format = false)
    {
        if(is_null($this->basePrice))
        {
            $this->basePrice = $this->getPropertyValue('MAX_PRICE');
            if(!$this->basePrice && $offer = $this->getMinPriceOffer())
            {
                $this->basePrice = $offer->getBasePrice();
            }
        }
        return $this->format($this->basePrice, $format);
    }

    public function isInFav($id){
        $favs = FavoritesRepository::getFav();
        if(in_array($id, $favs)){
            $fav = "Y";
        } else {
            $fav = "N";
        }
        return $fav;
    }

    public function getPrice($format = false)
    {
        if(is_null($this->price))
        {
            $this->price = $this->price ?: $this->getPropertyValue('MIN_PRICE');
            if(!$this->price && $offer = $this->getMinPriceOffer())
            {
                $this->price = $offer->getPrice();
            }
        }
        return $this->format(ceil($this->price), $format);
    }

    public function getMaxPrice($format = false)
    {
        return $this->getBasePrice($format);
    }

    /**
     * @return Offer
     */
    public function getMinPriceOffer()
    {
        $this->getOffers();
        return $this->offers['minPrice'];
    }

    /**
     * @return Offer[]
     */
    public function getOffers()
    {
        if(is_null($this->offers))
        {
            $this->obtainOffers();
        }
        return $this->offers['all'];
    }

    public function obtainOffers()
    {
        $offers = $this->offerRepositoryInstance->getByProductId($this->getId(), true);
        $this->setOffers($offers);
        return $this;
    }

    public function getCurrency()
    {
        return 'UAH';
    }

    private function format($price, $format = false)
    {
        return $format ? \CCurrencyLang::CurrencyFormat($price, $this->getCurrency()) : $price;
    }

    /**
     * @return Type|false
     */
    public function getType()
    {
        if(is_null($this->type))
        {
            $this->type = $this->typesRepositoryInstance->getByXmlId(
                $this->getTypeId()
            );
        }
        return $this->type;
    }

    public function getTypeId()
    {
        return $this->getPropertyValue('VID');
    }

    /**
     * @return Collection|false
     */
    public function getCollection()
    {
        if(is_null($this->collection))
        {
            $this->collection = $this->collectionsRepositoryInstance->getById(
                $this->getCollectionId()
            );
        }
        return $this->collection;
    }

    public function getSibling()
	{
        $siblings = $this->getSiblingsValue();

        $productCloth = [
            'id' => $this->getId(),
            'img' => $this->getClothImg()
        ];
        return count($siblings) > 0 ? array_merge([$productCloth], $siblings) : [$productCloth];
	}

	/*Выводить первые 4 цвета + "еще Х цветов"*/
    public function getSiblingsFormated($max = 3)
    {
        $result = [];
        $siblings = $this -> getSibling();
        $count = count($siblings);
        if($count > $max)
        {
            $siblings = array_slice($siblings, 0, $max);
            $result = ['ITEMS' => $siblings, 'DIFF' => $count - $max];
        }
        else $result['ITEMS'] = $siblings;
        return $result;
    }

    public function getSiblingSorted()
	{
	    $sibling = $this->getSibling();
	    usort($sibling, 'arrSort');
        return $sibling;
	}


    public function getSiblingsValue()
    {
        $result = $this->getPropertyValue(
            $this->getPropCode('siblings'),
            false,
            true
        );
        $arSiblings = json_decode($result, true);
        foreach ($arSiblings as $sibling)
        {
            if(substr_count($sibling['url'], '/catalog/') <= 0)
            {
                \Bitrix\Main\Diag\Debug::writeToFile($arSiblings,$this->getID(),"/local/logs/siblings.txt");
                continue;
            }
            $res[] = $sibling;

        }
        return $res;
    }

    public function getCollectionId()
    {
        return $this->getPropertyValue('COLLECTION');
    }

    public function getSeasonId()
    {
        return $this->getPropertyValue('SEASON');
    }

    public function getArticle()
    {
        return $this->getPropertyValue('CML2_ARTICLE');
    }

    public function getCloth()
    {
        return $this->getPropertyValueName('CLOTH');
    }

    public function getCollectionName()
    {
        return $this->getPropertyValueName('COLLECTION');
    }

    public function getSeasonName()
    {
        return $this->getPropertyValueName('SEASON');
    }

    public function getColor()
    {
        return $this->getPropertyValueName($this->getPropCode('color'));
    }

    public function getColorValue()
    {
        return array_shift($this->getPropertyValue($this->getPropCode('color')));
    }

    public function getModel()
    {
        return $this->getPropertyValue('MODEL');
    }

    public function getModelName()
    {
        return $this->getPropertyValueName('MODEL');
    }

    public function getConsist()
    {
        return $this->getPropertyValue('CONSIST');
    }

    public function getCare()
    {
        return $this->getPropertyValue('CARE');
    }

    public function getClothData()
    {
        $data = $this->getPropertyValueData('CLOTH');
        if(empty($data))
        {
            return false;
        }
        return array(
            'FILE' => $this->getFilePath($data['UF_FILE']),
            'NAME' => $data['UF_NAME'],
            'CODE' => $data['UF_CODE'],
            'DESCRIPTION' => $data['UF_DESCRIPTION'],
        );
    }

    public function getClothDescription()
    {
        $data = $this->getPropertyValueData('CLOTH');
        if(empty($data))
        {
            return false;
        }
        return $data['UF_DESCRIPTION'];
    }

    public function getClothColorName()
    {
        $data = $this->getPropertyValueData('CLOTH');
        if(empty($data))
        {
            return false;
        }
        return $data['UF_CONSIST'];
    }

    /*
     * Метод для выгруки в google adwords списка тканей
     * в виде полиэстер/вискоза/эластан
     */
    public function getClothes()
    {
        $data = $this->getPropertyValueData('CLOTH');
        if(empty($data) || substr_count($data['UF_CONSIST'], 'Состав') <= 0)
        {
            return false;
        }
        else
        {
            $consist = substr($data['UF_CONSIST'], strpos($data['UF_CONSIST'], 'Состав: ') + 9, strlen($data['UF_CONSIST']));
            $consist = str_replace("ё","е", $consist);
            $clothes = preg_replace ("/[^a-zа-яёЁ\s]/ui","", $consist);
            $clothes = preg_replace('| +|', '/', trim($clothes));
        }
        return $clothes;
    }

    /*
     * Метод для выгруки в lamoda списка тканей
     * в виде 5% вискоза, 40% полиэстер, 5% эластан
     */
    public function getLamodaClothes()
    {
        $data = $this->getPropertyValueData('CLOTH');
        if(empty($data) || substr_count($data['UF_CONSIST'], 'Состав') <= 0)
        {
            return false;
        }
        else
        {
            $consist = substr($data['UF_CONSIST'], strpos($data['UF_CONSIST'], 'Состав: ') + 8, strlen($data['UF_CONSIST']));
            $clothes = str_replace("ё","е", $consist);
        }
        return $clothes;
    }

    public function getColorData()
    {
        $data = $this->getPropertyValueData('COLOR');
        if(empty($data))
        {
            return false;
        }
        return array(
            'FILE' => $this->getFilePath($data['UF_FILE']),
            'NAME' => $data['UF_NAME'],
            'CODE' => $data['UF_CODE'],
            'CONSIST' => $data['UF_CONSIST']
        );
    }

    public function getClothImg()
    {
        $data = $this->getPropertyValueData('CLOTH');
        if(empty($data))
        {
            return false;
        }
        return $this->getFilePath($data['UF_FILE']);
    }

    public function getColorImg()
    {
        $data = $this->getPropertyValueData('COLOR');
        if(empty($data))
        {
            return false;
        }
        return $this->getFilePath($data['UF_FILE']);
    }

    public function getRecomendedProduct()
    {
        $data = $this->getPropertyValueData('RECOMMENDED_PRODUCT');
        if(empty($data))
        {
            return false;
        }
        return array_keys ($data);
    }

    public function getSizes()
    {
        $data = array();
        $arData = $this->getPropertyValueData('SIZE');

        if($arData){
            foreach ($arData as $arItem){
                $data[] = $arItem;
            }
        }
        return $data;
    }

    public function getSizesValue()
    {
        $data = array();
        $arData = $this->getPropertyValueData($this->getPropCode('size'));

        if($arData){
            foreach ($arData as $arItem){
                $data[] = $arItem;
            }
        }
        return $data;
    }

    public function getOffersSize()
    {
        $result = [];
        $offers = $this->getOffers();
        if(empty($offers))
        {
            return $result;
        }
        foreach($offers as $offer)
        {
            $size = $offer->getSizeData();
            $result[$size['UF_SORT']] = [
                'product_id' => $this->getID(),
                'offer_id' => $offer->getID(),
                'astrafit_id' => $offer->getAstrafitId(),
                'name' => $size['UF_NAME'],
                'name_ua' => $size['UF_NAME_RU'],
                'name_us' => $size['UF_NAME_US'],
				'name_eu' => $size['UF_NAME']
            ];
        }
        ksort($result);
        return $result;
    }

    public function getOfferSizesAvaliable(){
    	$sizes = self::getOffersSize();
    	if(count($sizes) > 0){
    		$html = "В наличии: ";
		    foreach ($sizes as $size){
			    $html .= "<span class='one-avaliable-size'>".$size["name"]."</span>";
		    }
	    } else {
    		$html = "<span class='one-avaliable-size'>Нет доступных размеров</span>";
	    }
    	return $html;
    }

    public function getFav()
    {
        global $USER;
        return FavoritesTable::getProductIds($USER->GetID());
    }

    public function getSale()
    {
        return $this->getPropertyValue('SALE');
    }

    public function getAvailable()
    {
        return $this->getPropertyValue('AVAILABLE');
    }

    public function getFreeShipping()
    {
        return $this->getPrice() >= FREE_DELIVERY_SUM;
    }

    public function getSeasonLamodaWear()
    {
        return $this->getPropertyValue('SEASON_WEAR');
    }

    public function getSeasonLamoda()
    {
        return $this->getPropertyValue('LAMODA_SEASON');
    }

    public function getStyle() //вернуть первое значение
    {
        $style = $this->getPropertyValue('STYLE');
        return reset($style);
    }

    public function getLamodaCategory()
    {
        $val = $this->getPropertyValue('SECTION_LAMODA');
        return preg_replace("/[^0-9]/", '', $val);
    }

    public function getLamodaTitle()
    {
        return $this->getPropertyValue('TITLE_LAMODA');
    }

    public function getLamodaPhoto($count)
    {
        $data = $this->getPropertyValue('LAMODA_PHOTO');
        if(!empty($data)) return $this->getImages($data, $count);
            else return false;
    }

    /*
     * В ламоде нет базового товара. ParentSku это название первого торгового предложения
     * Записываю в товар название первого по ИД ску.
     */
    public function getLamodaParentSku()
    {
        $data = $this->getPropertyValue('LAMODA_PARENT_SKU');
        if(empty($data)) $data = $this->setLamodaParentSku();
        return $data;
    }

    private function setLamodaParentSku()
    {
        $offers = $this->offerRepositoryInstance->getList(['ID' => 'ASC'], ['PROPERTY_CML2_LINK' => $this->getID()]);
        if(!empty($offers))
        {
            $parentOffer = reset($offers);
            \CIBlockElement::SetPropertyValuesEx($this->getID(), false, array('LAMODA_PARENT_SKU' => $parentOffer->getName()));
            return $parentOffer->getArticle();
        }
        else return $this->getArticle();
    }

    public function getAddedToLamoda()
    {
        return !empty($this->getPropertyValue('LAMODA_DATE'));
    }

    public function getClasp()
    {
        $data = $this->getPropertyValue('CLASP');
        if(empty($data)) $data = $this->getDefaultValue('CLASP');
        return $data;
    }

    public function getFiller()
    {
        $data = $this->getPropertyValue('FILLER_MATERIAL');
        if(empty($data)) $data = $this->getDefaultValue('FILLER_MATERIAL');
        return $data;
    }

    public function getLining()
    {
        $data = $this->getPropertyValue('LINING_MATERIAL');
        if(empty($data)) $data = $this->getDefaultValue('LINING_MATERIAL');
        return $data;
    }

    private function getDefaultValue($code)
    {
        return $this->defaultPropValues[$code];
    }

    /*Для google adwords*/
    public function getBarCode()
    {
        $res = $this->getPropertyValue('CML2_BAR_CODE');
        if(empty($res)) $res = $this->setBarCode();
        return $res;
    }

    public function showDiscountPercent()
    {
        $procent = $this->getPropertyValue('DISCOUNT_PROCENT');
        return !empty($procent) ? $procent : '%';
    }

    public function getTotalCount()
    {
        return $this->getPropertyValue('TOTAL_QUANTITY_SIZES');
    }

    private function setBarCode()
    {
        $offers = $this->getOffers();
        if (!empty($offers))
        {
            foreach ($offers as $offer)
            {
                $barCode = $offer->getBarCode();
                if (!empty($barCode))
                {
                    \CIBlockElement::SetPropertyValuesEx($this->getID(), false, array('CML2_BAR_CODE' => $barCode));
                    return $barCode;
                }
            }
        }
        return false;
    }

    public function getTrends()
    {
        return $this->getPropertyValue('TREND');
    }

    public function getAstrafitCode()
    {
        return !empty($this->getAstrafitByModel()) ? $this->getModelName() : $this->getArticle();
    }

    public function getAstrafitByModel()
    {
        return $this->getPropertyValue('ASRTAFIT_BY_MODEL') == 'Да';
    }

    public function getTags()
    {
        return $this->getPropertyValue('TAGS');
    }

    public function getTagsItems()
    {
        $tagsId = $this->getTags();
        if(empty($tagsId))
            return false;

        $tags = $this->tagsRepositoryInstance->getList(['SORT' => 'desc'], ['ID' => $tagsId]);
        if(!empty($tags))
        {
            foreach ($tags as $tag)
            {
                $result[] = [
                    'NAME' => $tag->getName(),
                    'URL' => $tag->getUrl()
                ];
            }

            return $result;
        }
        return false;
    }

    public function showSizeTypes()
    {
        $sectionRepository = app('ProductSectionsRepository');
        $parentSection = $sectionRepository->getById($this->getFirstLevelSection());
        return !$parentSection->getNotShowSizeType();
    }

}
