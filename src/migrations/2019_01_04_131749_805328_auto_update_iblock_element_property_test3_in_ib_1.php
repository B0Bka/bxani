<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoUpdateIblockElementPropertyTest3InIb120190104131749805328 extends BitrixMigration
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
  'ID' => 3,
  'NAME' => 'Test 3',
  'SORT' => 500,
  'CODE' => 'test3',
  'MULTIPLE' => 'N',
  'IS_REQUIRED' => 'N',
  'ACTIVE' => 'N',
  'USER_TYPE' => false,
  'PROPERTY_TYPE' => 'S',
  'IBLOCK_ID' => 1,
  'FILE_TYPE' => '',
  'LIST_TYPE' => 'L',
  'ROW_COUNT' => 1,
  'COL_COUNT' => 30,
  'LINK_IBLOCK_ID' => 0,
  'DEFAULT_VALUE' => '',
  'USER_TYPE_SETTINGS' => 
  array (
  ),
  'WITH_DESCRIPTION' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'Y',
  'MULTIPLE_CNT' => 5,
  'HINT' => '',
  'VALUES' => 
  array (
  ),
  'SECTION_PROPERTY' => 'Y',
  'SMART_FILTER' => 'Y',
  'DISPLAY_TYPE' => 'F',
  'DISPLAY_EXPANDED' => 'Y',
  'FILTER_HINT' => '',
  'FEATURES' => 
  array (
    'iblock:DETAIL_PAGE_SHOW' => 
    array (
      'ID' => '4',
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
    'iblock:LIST_PAGE_SHOW' => 
    array (
      'ID' => '3',
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'LIST_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
  ),
  'DEL' => 'N',
);

        $id = $this->getIblockPropIdByCode('test3', 1);
        $fields['ID'] = $id;

        $ibp = new CIBlockProperty();
        $updated = $ibp->update($id, $fields);

        if (!$updated) {
            throw new MigrationException('Ошибка при изменении свойства инфоблока '.$ibp->LAST_ERROR);
        }
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     */
    public function down()
    {
        return false;
    }
}
