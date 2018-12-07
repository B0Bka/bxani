<?php
namespace Aniart\Main\Orm;

use Bitrix\Main\Entity;
use Bitrix\Main\Application;

class SortTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_hlbd_sort';
    }

    public static function getUfId()
    {
        return 'HLBLOCK_18';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\IntegerField('ITEM', array(
                'required' => true,
                'column_name' => 'UF_ITEM'
            )),
            new Entity\IntegerField('SECTION', array(
                'required' => true,
                'column_name' => 'UF_SECTION'
            )),
            new Entity\IntegerField('SORT', array(
                'required' => true,
                'column_name' => 'UF_SORT'
            )),
            new Entity\StringField('TYPE', array(
                'required' => true,
                'column_name' => 'UF_TYPE'
            )),
        );
    }

    public static function setSort($itemId, $sort, $section, $type)
    {
        if($id = self::itemExist($itemId, $section, $type))
            self::updateItem($id, $sort);
        else
            self::addItem($itemId, $section, $sort, $type);
    }

    public static function setSortCron($itemId, $sort, $section, $type) //для крона не перезаписывать сортировку
    {
        if(!self::itemExist($itemId, $section, $type))
        {
            self::addItem($itemId, $section, $sort, $type);
        }
    }

    public static function itemExist($id, $section, $type)
    {
        $result = self::getList(array('filter' => ['ITEM' => $id, 'SECTION' => $section, 'TYPE' => $type], 'select' => ['ID']))->fetchAll();
        if(empty($result))
            return false;
        else
            return $result[0]['ID'];
    }

    public static function updateItem($id, $sort)
    {
        return self::update($id, array(
            'SORT' => $sort
        ));
    }

    public static function addItem($id, $section, $sort, $type)
    {
        return self::add(array(
            'ITEM' => $id,
            'SECTION' => $section,
            'SORT' => $sort,
            'TYPE' => $type
        ));
    }

    public static function getItemSort($itemId, $section, $type)
    {
        $res = self::getList(['filter' => ['ITEM' => $itemId, 'SECTION' => $section, 'TYPE' => $type]])->fetchAll();
        return $res[0]['SORT'];
    }

    public static function getProducts()
    {
        $rows = [];
        $res = self::getList(['group' => ['ITEM']]);
        while ($row = $res->fetch())
        {
            if(!in_array($row['ITEM'], $rows)) $rows[] = $row['ITEM'];
        }
        return $rows;
    }

    public static function deleteProduct($id)
    {
        $connection = Application::getConnection();
        $sql = "DELETE FROM ".self::getTableName()." WHERE UF_ITEM=".$id;
        $connection->queryExecute($sql);
    }

    public static function getItemAllSort($itemId)
    {
        $res = self::getList(['filter' => ['ITEM' => $itemId]])->fetchAll();
        foreach ($res as $item)
        {
            $arRes[$item['TYPE']][$item['SECTION']] = $item['ID'];
        }
        return $arRes;
    }

    public function deleteById($id)
    {
        $connection = Application::getConnection();
        $sql = "DELETE FROM ".self::getTableName()." WHERE ID=".$id;
        $connection->queryExecute($sql);
    }

    public function deleteByItemAndType($id, $type)
    {
        $connection = Application::getConnection();
        $sql = "DELETE FROM ".self::getTableName()." WHERE UF_ITEM=".$id.' AND UF_TYPE="'.$type.'"';
        $connection->queryExecute($sql);
    }
}