<?php

namespace Aniart\Main\Repositories;

use Aniart\Main\Interfaces\ErrorableInterface,
    Aniart\Main\Traits\ErrorTrait;

abstract class AbstractStoresRepository implements ErrorableInterface
{
    
    use ErrorTrait;

    /**
     * @var \CDBResult
     */
    protected $dbResult;

    /**
     * @param array $fields
     * @return \Aniart\Main\Models\AbstractModel
     */
    abstract public function newInstance(array $fields = []);

    public function getLastDBResult()
    {
        return $this->dbResult;
    }

}
