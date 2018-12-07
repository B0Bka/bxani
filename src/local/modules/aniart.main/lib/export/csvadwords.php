<?php
namespace Aniart\Main\Export;
/*
 * Создание csv для google adwords
 */
class CsvAdwords extends AbstractCsvExport
{
    protected $fp,         
            $arHead = ['ID', 'Item title', 'Final URL','Image URL', 'Price',
                'Sale Price', 'Item subtitle', 'Item description'
            ];
            
    public function init()
    {
        $this->fp = $this->PrepareFile($this->fileName. '.tmp');
        $this->writeHead();
        $this->BuildOffers();
        $this->CloseFile($this->fp);
        unlink($_SERVER['DOCUMENT_ROOT'] . $this->fileName);
        rename($_SERVER['DOCUMENT_ROOT'] . $this->fileName. '.tmp', $_SERVER['DOCUMENT_ROOT'] . $this->fileName);
    }

    private function writeHead()
    {
        $this->writeRow($this->arHead, $this->fp);
    }
    
    protected function BuildOffers()
    {
        $page = 1;
        // Set filter
        $filter = ['!PROPERTY_AVAILABLE' => false, 'ACTIVE' => 'Y', 'SECTION_GLOBAL_ACTIVE' => 'Y'];
        $order = ['ID' => 'ASC'];
        $arNavStatParams = array(
            "iNumPage" => $page,
            "nPageSize" => $this->step,
        );

        $productsRepository = app('ProductsRepository');
        do {
            $products = $productsRepository->getList(
                $order,
                $filter,
                false,
                $arNavStatParams
            );
            $arProducts = array_values($products);

            foreach($arProducts as $product)
            {
                $image = $product->getPropertyValue($product->getPropCode('more_photo'));
                $pic = reset($product->getMorePhoto($image, 1, 300, 300));
                $text = trim(strip_tags($product->getDetailText()));
                $text = str_replace('&nbsp;', '', $text);
                //$text =  mbCutString($text, 500);
                $price = $product->getBasePrice();
                $discount = $product->getPrice();
                $discount = $price > $discount ? $discount.' UAH' : '';
                $productData = [
                                'ID' => $product->getId(),
                                'NAME' => $product->getName(),
                                'URL' => $this->siteDir.$product->getUrl(),
                                'IMAGE' => $this->siteDir.$pic['src'],
                                'PRICE' => $price.' UAH',
                                'DISCOUNT' => $discount,
                                'TITLE' => $this->getTitle($product),
                                'TEXT' => $this->getText($text)
                                ];
                $this->writeRow($productData, $this->fp);
            }
            $page++;
            $arNavStatParams['iNumPage'] = $page;
            

        }while(count($products) == $this->step);
        unset($arProducts);
    }

    private function getTitle($obProduct)
    {
        $min = 0;
        $max = 0;
        $offers = $obProduct->getOffers();
        foreach($offers as $offer)
        {
            if($offer->isAvailable())
            {
                $size = $offer->getSize();
                if($min == 0) $min = $size;

                if($size > $max) $max = $size;
                    elseif($size < $min) $min = $size;
            }
            if($min == $max) $str = $min;
                else $str = $min.'-'.$max;
        }
        return 'Размеры: '.$str;
    }
    
    private function getText($text)
    {
        $pos=strpos($text, ".");
        return substr($text, 0, $pos);
    }
}