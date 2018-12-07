<?php


namespace Aniart\Main\Services\Catalog;

use Aniart\Main\Repositories\ProductsRepository;
use Aniart\Main\Traits\ProductPropCodeTrait;
use Aniart\Main\Exceptions\AniartException;
use Cutil;

class CharacterCodeGenerate extends Service
{
    
    use ProductPropCodeTrait;
    
    /**
     * @var ProductsRepository
     */
    private $productsRepository;
    
    protected $code;
    protected $product;
    protected $properties;
    protected $updateFrom = '08.05.2018'; //дата создания задачи NB-211. Нельзя перетирать код уже созданных товаров

    public function __construct($code, $product, $properties)
    {
        parent::__construct();
        $this->productsRepository = app('ProductsRepository');
        
        $this->code = $code;
        $this->product = $product;
        $this->properties = $properties;
    }
    /*
     * код формируется по принципу "Название для показа-Артикул"
     */
    public function generate()
    {
        $propUpdated = $this->properties[$this->getPropertyId()];
        if(empty($propUpdated))
        {
            return $this->code;
            /**
             * для установки для всех товаров. консоль
             *
            $product = $this->productsRepository->getById($this->product);
            $propUpdated[] = ['VALUE' => $product->getName()];
            /**/
        }
        foreach($propUpdated as $prop)
        {

            if(empty($prop['VALUE']))
            {
                continue;
            }
            $code = Cutil::translit($prop['VALUE'], 'ru', [
                'replace_space' => '_',
                'replace_other' => '_'
            ]);
            $this->code = $this->getUniquenessCode($code);
            
            //only the first element if multiple
            break;
        }
        return $this->code;
    }
    
    public function getCode()
    {
        return $this->generate();
    }

    public function getPropertyId()
    {
        return $this->getPropertyIdByCode($this->getPropCode('name_show'));
    }

    public function getPropertyArticleId()
    {
        return $this->getPropertyIdByCode($this->getPropCode('article'));
    }

    public function getUniquenessCode($code)
    {
        $propArticle = $this->properties[$this->getPropertyArticleId()];
        if(empty($propArticle))
        {
            return $code;
        }
        foreach($propArticle as $prop)
        {
            if(empty($prop['VALUE']))
            {
                continue;
            }
            $article = Cutil::translit($prop['VALUE'], 'ru', [
                'replace_space' => '-',
                'replace_other' => '-'
            ]);
            return $code.'-'.$article;
        }
    }

    /*Для товаров созданных до 08.05*/
        public function getOldCode()
    {
        $propUpdated = $this->properties[$this->getPropertyId()];
        if(empty($propUpdated))
        {
            return $this->code;
        }
        foreach($propUpdated as $prop)
        {
            if(empty($prop['VALUE']))
            {
                continue;
            }
            $code = Cutil::translit($prop['VALUE'], 'ru', [
                'replace_space' => '_',
                'replace_other' => '_'
            ]);
            $this->code = $this->getOldUniquenessCode($code);
            break;
        }
        return $this->code;
    }
    public function getOldUniquenessCode($code)
    {
        $products = $this->productsRepository->getList([], [
            '!=ID' => $this->product,
            'CODE' => $code
        ]);
        if(empty($products))
        {
            return $code;
        }
        foreach($products as $product)
        {
            if($product->getId() == $this->product)
            {
                continue;
            }
            $code = $this->getOldUniquenessCode("{$code}_1");
        }
        return $code;
    }

}