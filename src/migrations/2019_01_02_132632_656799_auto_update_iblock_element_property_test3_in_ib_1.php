<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoUpdateIblockElementPropertyTest3InIb120190102132632656799 extends BitrixMigration
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
  'ACTIVE' => 'Y',
  'IBLOCK_ID' => 1,
  'LINK_IBLOCK_ID' => NULL,
  'NAME' => 'Test 3',
  'SORT' => '500',
  'CODE' => 'test3',
  'MULTIPLE' => 'N',
  'IS_REQUIRED' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'Y',
  'WITH_DESCRIPTION' => 'N',
  'MULTIPLE_CNT' => '5',
  'HINT' => '',
  'ROW_COUNT' => '1',
  'COL_COUNT' => '30',
  'DEFAULT_VALUE' => '',
  'LIST_TYPE' => 'L',
  'USER_TYPE_SETTINGS' => NULL,
  'FILE_TYPE' => '',
  'SECTION_PROPERTY' => 'N',
  'SMART_FILTER' => 'N',
  'DISPLAY_TYPE' => 'F',
  'DISPLAY_EXPANDED' => 'N',
  'FILTER_HINT' => '',
  'PROPERTY_TYPE' => 'S',
  'USER_TYPE' => '',
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
  ),
  'ID' => 3,
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
