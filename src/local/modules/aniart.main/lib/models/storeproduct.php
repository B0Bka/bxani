<?php


namespace Aniart\Main\Models;

class StoreProduct extends AbstractModel
{

    public function getStoreId()
    {
        return $this->fields['STORE_ID'];
    }
    
    public function getName()
    {
        return $this->fields['STORE_NAME'];
    }

    public function getAmount()
	{
		return $this->fields['AMOUNT'];
	}
    
    public function getPhone()
    {
        return $this->fields['STORE_PHONE'];
    }
    
}