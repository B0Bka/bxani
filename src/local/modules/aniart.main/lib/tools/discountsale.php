<?php
namespace Aniart\Main\Tools;
use CIBlockElement;
class DiscountSale
{
    private $saleItems = [];

    public function init()
    {
        if($this->checkRunning()) return false;//проверить запущена ли выгрузка

        \Bitrix\Main\Diag\Debug::writeToFile(date("d.m.Y H:i:s"),"DiscountSale start","/local/logs/cron.txt");
        \COption::SetOptionString("aniart","run_sale","R"); //Y - нужно запустить в ручном N - не стоит запуск в ручном режиме R - вигрузка запущена
        $this->saleItems = $this->getItems();
        $this->setSale();
        //$this->setTime();
        $this->doFinalActions();
    }

    private function getItems()
    {
        $arItems = [];
        $discounts =  \Bitrix\Sale\Internals\DiscountTable::getList(['filter' => ['ACTIVE' => "Y"]])->fetchAll();
        foreach($discounts as $disacount)
        {
            if(!$this->checkDate($disacount['ACTIVE_FROM'], $disacount['ACTIVE_TO'])) continue;
            foreach($disacount['ACTIONS_LIST']['CHILDREN'] as $action)
            {
                if($action['CLASS_ID'] == 'ActSaleBsktGrp')
                    foreach ($action['CHILDREN'] as $basketGroup)
                    {
                        if($basketGroup['CLASS_ID'] == 'ActSaleSubGrp')
                            foreach ($basketGroup['CHILDREN'] as $basketSubGroup)
                            {
                                if($basketSubGroup['CLASS_ID'] == 'CondIBElement')
                                {
                                    if(!empty($basketSubGroup['DATA']['value']))
                                    {
                                        $arItems = array_merge($arItems, $basketSubGroup['DATA']['value']);
                                    }
                                }

                            }
                    }
            }
        }
        return array_unique($arItems);
    }

    private function setSale()
    {
        $el = new CIBlockElement;
        $allSaleItems = $this->getSaleItems();
        $diff = array_diff($allSaleItems, $this->saleItems);
        if(!empty($diff))
            $this->setEmptySale($diff);
        foreach($this->saleItems as $id)
        {
           CIBlockElement::SetPropertyValuesEx($id, PRODUCTS_IBLOCK_ID, ['SALE' => SALE_PROP_ENUM_ID]);
           //dBug($id);
           //$res = $el->Update($id, ['MODIFIED_BY' => 1]);
        }
    }

    /*
     * если скидка обновлена после последнего запуска крона
    private function isNew($obDate)
    {
        $time = $obDate->getTimestamp();
        $lastRun = \COption::GetOptionString("aniart", "run_discount_sale");
        if($time > $lastRun) return true;
            else return false;
    }
    private function setTime()
    {
        \COption::SetOptionString("aniart", "run_discount_sale", time());
    }
    */

    private function checkDate($obDateFrom, $obDateTo)
    {
        if(empty($obDateFrom) && empty($obDateTo)) return true;
        if(!empty($obDateFrom))
        {
            $activeFrom = $obDateFrom->getTimestamp();
        }
        if(!empty($obDateTo))
        {
            $activeTo = $obDateTo->getTimestamp();
        }

        $now = time();
        if($now > $activeFrom && empty($obDateTo)) return true;
        if(empty($obDateFrom) && $now < $activeTo) return true;
        if($now > $activeFrom && $now < $activeTo) return true;
        return false;
    }

    private function getSaleItems()
    {
        $items = [];
        $products = app('ProductsRepository')->getList([], ['!PROPERTY_SALE' => false]);
        foreach ($products as $product) $items[] = $product->getId();
        return $items;
    }

    private function setEmptySale($arId)
    {
        foreach($arId as $id)
        {
           CIBlockElement::SetPropertyValuesEx($id, PRODUCTS_IBLOCK_ID, ['SALE' => false]);
        }
    }
    
    private function checkRunning()
    {
        $now = time();
        $time = strtotime(\COption::GetOptionString("aniart","date_sale"));
        $diff = (($now - $time) / 60);
        if(\COption::GetOptionString("aniart","run_sale") == 'R' && $diff > $this->runTime) //если предыдущая выгрузка не отработала до конца
        {
            \COption::SetOptionString("aniart","run_sale","N");
            return false;
        }
        elseif(\COption::GetOptionString("aniart","run_sale") == 'R')
            return true;

        return false;
    }

    private function doFinalActions()
    {
        \COption::SetOptionString("aniart","run_sale","N");
        \COption::SetOptionString("aniart","date_sale", date('d.m.Y H:i:s'));
        \Bitrix\Main\Diag\Debug::writeToFile(date("d.m.Y H:i:s"),"DiscountSale finish","/local/logs/cron.txt");
    }
}