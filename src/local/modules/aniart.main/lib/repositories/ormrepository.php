<?php

/**
 * Created by PhpStorm.
 * User: damian
 * Date: 04.12.14
 * Time: 17:23
 */

namespace Aniart\Main\Repositories;

use Aniart\Main\Interfaces\ErrorableInterface;
use Aniart\Main\Models\AbstractModel;
use Aniart\Main\Traits\ErrorTrait;

abstract class OrmRepository implements ErrorableInterface
{

    use ErrorTrait;

    abstract public function newInstance(array $fields = array());

    public function getList($table, $params)
    {
        $elements = $table::getList($params);
        while($arElement = $elements->fetch())
        {
           $result[] =  $this->newInstance($arElement);
        }
        return $result;
    }

}
