<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoAddIblockElementPropertyListToIb120190103130432279952 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function up()
    {
        $fields = array (
  'ID' => 0,
  'NAME' => 'List',
  'SORT' => 500,
  'CODE' => 'list',
  'MULTIPLE' => 'Y',
  'IS_REQUIRED' => 'N',
  'ACTIVE' => 'Y',
  'USER_TYPE' => false,
  'PROPERTY_TYPE' => 'L',
  'IBLOCK_ID' => 1,
  'FILE_TYPE' => '',
  'LIST_TYPE' => 'L',
  'ROW_COUNT' => 1,
  'COL_COUNT' => 30,
  'LINK_IBLOCK_ID' => 0,
  'DEFAULT_VALUE' => '',
  'USER_TYPE_SETTINGS' => false,
  'WITH_DESCRIPTION' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'N',
  'MULTIPLE_CNT' => 5,
  'HINT' => '',
  'VALUES' => 
  array (
    'n0' => 
    array (
      'ID' => 'n0',
      'VALUE' => '101',
      'XML_ID' => '',
      'SORT' => 500,
      'DEF' => 'N',
    ),
    'n1' => 
    array (
      'ID' => 'n1',
      'VALUE' => '202',
      'XML_ID' => '',
      'SORT' => 500,
      'DEF' => 'N',
    ),
    'n2' => 
    array (
      'ID' => 'n2',
      'VALUE' => '303',
      'XML_ID' => '',
      'SORT' => 500,
      'DEF' => 'N',
    ),
    'n3' => 
    array (
      'ID' => 'n3',
      'VALUE' => '404',
      'XML_ID' => '',
      'SORT' => 500,
      'DEF' => 'N',
    ),
  ),
  'SECTION_PROPERTY' => 'Y',
  'SMART_FILTER' => 'N',
  'DISPLAY_TYPE' => 'F',
  'DISPLAY_EXPANDED' => 'N',
  'FILTER_HINT' => '',
  'FEATURES' => 
  array (
    'iblock:LIST_PAGE_SHOW' => 
    array (
      'ID' => 'n0',
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'LIST_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
    'iblock:DETAIL_PAGE_SHOW' => 
    array (
      'ID' => 'n1',
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
    'catalog:IN_BASKET' => 
    array (
      'ID' => 'n2',
      'MODULE_ID' => 'catalog',
      'FEATURE_ID' => 'IN_BASKET',
      'IS_ENABLED' => 'N',
    ),
  ),
);

        $ibp = new CIBlockProperty();
        $propId = $ibp->add($fields);

        if (!$propId) {
            throw new MigrationException('Ошибка при добавлении свойства инфоблока '.$ibp->LAST_ERROR);
        }
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function down()
    {
        $id = $this->getIblockPropIdByCode('list', 1);

        $ibp = new CIBlockProperty();
        $deleted = $ibp->delete($id);

        if (!$deleted) {
            throw new MigrationException('Ошибка при удалении свойства инфоблока '.$ibp->LAST_ERROR);
        }
    }
}
