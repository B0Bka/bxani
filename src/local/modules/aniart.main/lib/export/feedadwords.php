<?php
namespace Aniart\Main\Export;
/*
 * Создание фида для google adwords и facebook
 *   $fields = ['id', 'item_group_id', 'mpn','title', 'description','google_product_category',
 *  'product_type', 'link', 'availability', 'price', 'sale_price', 'brand', 'gender', 'age_group',
    'color', 'image_link', 'additional_image_link', 'condition', 'adult', 'material',
    'size', 'size_​type', 'size_​system','custom_label_0', 'custom_label_1', 'custom_label_2'],
 */
class FeedAdwords extends AbstractExport
{
    private $fp,
            $available = 'in stock',
            $brand = 'Natali Bolgar',
            $gender = 'female',
            $ageGroup = 'adult',
            $condition = 'new',
            $adult = 'no',
            $sizeSystem = 'EU',
            $sizeType = 'regular',
            $rootStr = 'Natali Bolgar > Каталог > ',
            $new = 'new',
            $stock = 'stock',
            $runTime = 15; //минуты

    public function init()
    {
        if($this->checkRunning()) return false;//проверить запущена ли выгрузка

        \COption::SetOptionString("aniart","run_feed","R"); //Y - нужно запустить в ручном N - не стоит запуск в ручном режиме R - вигрузка запущена
		\Bitrix\Main\Diag\Debug::writeToFile(date("d.m.Y H:i:s"),"adwords start","/local/logs/cron.txt");
        $this->fp = $this->PrepareFile($this->fileName. '.tmp');
        $this->PreWriteCatalog2($this->fp);

        $this->BuildOffers();

        $this->PostWriteCatalog2($this->fp);

        $this->CloseFile($this->fp);
        unlink($_SERVER['DOCUMENT_ROOT'] . $this->fileName);
        rename($_SERVER['DOCUMENT_ROOT'] . $this->fileName. '.tmp', $_SERVER['DOCUMENT_ROOT'] . $this->fileName);
        $this->doFinalActions();
    }

    private function getSections()
    {
        $productsSectionRepository = app('ProductSectionsRepository');
        $sections = $productsSectionRepository->getList(
            ['ID' => 'ASC'],
            []
        );
        foreach($sections as $section)
        {
            $arSection[$section->getId()] = $section->getGoogleID();
        }
        return $arSection;
    }

    private function getRange($price)
    {
        if($price <= 1000)
            return '0-1000';
        elseif($price > 1000 && $price <= 2000)
            return '1001-2000';
        elseif($price > 2000 && $price <= 3500)
            return '2001-3500';
        elseif($price > 3500)
            return '3500';
    }

    private function getAddImage($arImages)
    {
        $str = '';
        if(count($arImages) > 2)
        {
            foreach($arImages as $key => $image)
            {
                if($key == 0) continue; //первая картинка идет в основную, остальные в additional
                $str .= "<g:additional_image_link>" .$this->siteDir.$image. "</g:additional_image_link>\n";
            }
            return $str;
        }
        else return false;
    }

    public function PreWriteCatalog2($fp)
    {
        @fwrite($fp, "<rss xmlns:g=\"http://base.google.com/ns/1.0\" version=\"2.0\">
            <channel>
            <title>Shopping Feed</title>
            <link>https://natalibolgar.com</link>\n"
        );
    }
    public function PostWriteCatalog2($fp)
    {
        @fwrite($fp, "</channel>\n
            </rss>\n");
    }

    protected function BuildOffers()
    {
        $arSection = $this->getSections();
        $page = 1;
        // Set filter
        $filter = ['!PROPERTY_AVAILABLE' => false, 'ACTIVE' => 'Y', 'SECTION_GLOBAL_ACTIVE' => 'Y', '>PROPERTY_TOTAL_QUANTITY_SIZES' => 0];
        $order = ['ID' => 'ASC'];
        $arNavStatParams = array(
            "iNumPage" => $page,
            "nPageSize" => $this->step,
        );
        $arSelect = ['ID', 'IBLOCK_SECTION_ID, DETAIL_TEXT', 'PROPERTY_MORE_PHOTO', 'PROPERTY_COLOR', 'DETAIL_PAGE_URL', 'PROPERTY_CLOTH',
                    'PROPERTY_COLLECTION', 'PROPERTY_SEASON', 'PROPERTY_MODEL', 'PROPERTY_SALE', 'PROPERTY_TOTAL_QUANTITY_SIZES'
        ];

        $productsRepository = app('ProductsRepository');
        do {
            $products = $productsRepository->getList(
                $order,
                $filter,
                false,
                $arNavStatParams,
                $arSelect
            );
            $arProducts = array_values($products);

            foreach($arProducts as $product)
            {
                $sections = $product->getNavChain();
                $itemGroup = [];
                foreach($sections as $section)
                {
                    $itemGroup[] = $section['NAME'];
                }
                $lastSection = end($itemGroup);
                $itemGroupStr = $this->rootStr.implode(' > ', $itemGroup);
                $obSection = reset($product->getSections());
                $text = trim(strip_tags($product->getDetailText()));
                $text = str_replace('&nbsp;', '', $text);
                $text =  mbCutString($text, 500);
                $price = $product->getBasePrice();
                $discount = $product->getPrice();
                $productData = [
                                'ID' => $product->getId(),
                                'TYPE' => $itemGroupStr,
                                'SECTION' => $lastSection   ,
                                'TEXT' => $text,
                                'IMAGE' => $product->getFullPhoto(),
                                'SECTION_ID' => $obSection->getId(),
                                'DISCOUNT' => $discount.' UAH',
                                'PRICE' => $price.' UAH',
                                'NAME' => $product->getName(),
                                'URL' => $this->siteDir.$product->getDetailPageUrl(),
                                'COLOR' => reset($product->getColor()),
                                'CLOTH' => $product->getClothes(),
                                'SEASON' => $product->getSeasonName(),
                                'MODEL' => $product->getModelName(),
                                'BARECODE' => $product->getBarCode(),
                                'PRICE_DIFF' => $price - $discount,
                                'SALE' => $product->getSale(),
                                'COUNT' => $product->getTotalCount(),
                                'COLLECTION' => $product->getCollectionName(),
                                ];
                $stringOffers = $this->BuildOffer($productData, $arSection);
                $this->WriteOffers($this->fp, $stringOffers);
            }
            $page++;
            $arNavStatParams['iNumPage'] = $page;

        }while(count($products) == $this->step);
        unset($arProducts);
    }

    protected function BuildOffer($product, $arSection)
    {
        $offer = "";
        $offer .= "<item>\n";

        $offer .= "<g:id>" .$product['ID'] . "</g:id>\n";
        $offer .= "<g:item_group_id>" .$product['MODEL'] . "</g:item_group_id>\n";
        $offer .= "<g:mpn>" . $product['BARECODE'] . "</g:mpn>\n";
        $offer .= "<g:title>" .$product['NAME']. "</g:title>\n";
        $offer .= "<g:description>" .$product['TEXT']. "</g:description>\n";
        $offer .= "<g:google_product_category>" .$arSection[$product['SECTION_ID']] . "</g:google_product_category>\n";
        $offer .= "<g:product_type>" . $product['TYPE'] . "</g:product_type>\n";
        $offer .= "<g:link>" . $product['URL'] . "</g:link>\n";
        $offer .= "<g:availability>" .$this->available. "</g:availability>\n";
        $offer .= "<g:price>" .$product['PRICE'] . "</g:price>\n";
        $offer .= "<g:sale_price>" .$product['DISCOUNT'] . "</g:sale_price>\n";
        $offer .= "<g:brand>" .$this->brand. "</g:brand>\n";
        $offer .= "<g:gender>" .$this->gender . "</g:gender>\n";
        $offer .= "<g:age_group>" .$this->ageGroup . "</g:age_group>\n";
        $offer .= "<g:color>" .$product['COLOR'] . "</g:color>\n";
        $offer .= "<g:image_link>" .$this->siteDir.$product['IMAGE'][0] . "</g:image_link>\n";
        $offer .= $this->getAddImage($product['IMAGE']);
        $offer .= "<g:condition>" .$this->condition . "</g:condition>\n";
        $offer .= "<g:adult>" .$this->adult. "</g:adult>\n";
        $offer .= "<g:material>" .$product['CLOTH']. "</g:material>\n";
        $offer .= "<g:size_type>" .$this->sizeType. "</g:size_type>\n";
        $offer .= "<g:size_system>" .$this->sizeSystem. "</g:size_system>\n";
        $offer .= "<g:custom_label_0>" .$product['SECTION']. "</g:custom_label_0>\n";
        $offer .= "<g:custom_label_1>" .$product['SEASON']. "</g:custom_label_1>\n";
        //$offer .= "<g:custom_label_2>" .$this->getRange($product['PRICE']). "</g:custom_label_2>\n";
        $offer .= "<g:custom_label_2>" .$product['COLLECTION']. "</g:custom_label_2>\n";
        $offer .= "<g:custom_label_3>" .$this->getStock($product). "</g:custom_label_3>\n";
        $offer .= "<g:custom_label_4>" .$product['COUNT']. "</g:custom_label_4>\n";
        $offer.= "</item>\n";
        return $offer;
    }

    private function getStock($product)
    {
        if($product['SALE'] || $product['PRICE_DIFF'] > 0)  return $this->stock;
            else return $this->new;
    }

    private function checkRunning()
    {
        $now = time();
        $time = strtotime(\COption::GetOptionString("aniart","date_feed"));
        $diff = (($now - $time) / 60);
        if(\COption::GetOptionString("aniart","run_feed") == 'R' && $diff > $this->runTime) //если предыдущая выгрузка не отработала до конца
        {
            \COption::SetOptionString("aniart","run_feed","N");
            return false;
        }
        elseif(\COption::GetOptionString("aniart","run_feed") == 'R')
            return true;

        return false;
    }

    private function doFinalActions()
    {
        \COption::SetOptionString("aniart","run_feed","N");
        \COption::SetOptionString("aniart","date_feed", date('d.m.Y H:i:s'));
        \Bitrix\Main\Diag\Debug::writeToFile(date("d.m.Y H:i:s"),"adwords finish","/local/logs/cron.txt");
    }
}