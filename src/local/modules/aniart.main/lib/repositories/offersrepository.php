<?

namespace Aniart\Main\Repositories;

use Aniart\Main\Cacher\AbstractCacheCell;

class OffersRepository extends AbstractIblockElementRepository
{

    protected $pricesTypes;
    protected $pricesCodes;
    protected $selectedFields = [
        'ID', 
        'IBLOCK_ID', 
        'NAME', 
        'CODE', 
        'ACTIVE',
        'PROPERTY_SIZE', 
        'PROPERTY_CML2_LINK', 
        'PROPERTY_ASTRAFIT_ID'
    ];

    public function __construct($iblockId, array $pricesCodes)
    {
        parent::__construct($iblockId);
        $this->pricesCodes = $pricesCodes;
        foreach($this->getPricesTypes() as $priceType)
        {
            $this->selectedFields[] = $priceType['SELECT'];
        }
    }

    /**
     * @param array $fields
     * @return \Aniart\Main\Models\Product
     */
    public function newInstance(array $fields = array())
    {
        $fields['PRICE_TYPES'] = $this->getPricesTypes();
        return app('Offer', array($fields));
    }

    public function getPricesTypes()
    {
        if(is_null($this->pricesTypes))
        {
            /**
             * @var AbstractCacheCell $cacheCell
             */
            $cacheKey = implode('::', $this->pricesCodes);
            $cacheCell = app('CacheCell', [$cacheKey, 36000]);
            $pricesTypes = $cacheCell->load();
            if(is_null($pricesTypes))
            {
                $pricesTypes = \CIBlockPriceTools::GetCatalogPrices($this->getIBlockId(), $this->pricesCodes);
                $cacheCell->save($pricesTypes);
            }
            $this->pricesTypes = $pricesTypes;
        }
        return $this->pricesTypes;
    }

    public function getByProductId($productId, $onlyActive = false)
    {
        $productId = (int) $productId;
        if($productId <= 0)
        {
            return [];
        }
        $filter = [
            '>CATALOG_QUANTITY' => 0,
            'PROPERTY_CML2_LINK' => $productId,
            'CATALOG_AVAILABLE' => 'Y'
        ];
        if($onlyActive)
        {
            $filter['ACTIVE'] = 'Y';
        }
        return $this->getList(['SORT' => 'ASC'], $filter);
    }

    public function getAllActiveByProductId($productId)
    {
        $productId = (int) $productId;
        if($productId <= 0)
        {
            return [];
        }
        $filter = [
            'PROPERTY_CML2_LINK' => $productId,
        ];
        return $this->getList(['SORT' => 'ASC'], $filter);
    }
}
