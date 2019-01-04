<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoUpdateIblockElementPropertyTest2InIb120190104131749785528 extends BitrixMigration
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
  'ID' => 2,
  'NAME' => 'Test 2',
  'SORT' => 500,
  'CODE' => 'test2',
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
  'FILTRABLE' => 'N',
  'MULTIPLE_CNT' => 5,
  'HINT' => '',
  'VALUES' => 
  array (
  ),
  'SECTION_PROPERTY' => 'Y',
  'SMART_FILTER' => 'Y',
  'DISPLAY_TYPE' => 'F',
  'DISPLAY_EXPANDED' => 'N',
  'FILTER_HINT' => '',
  'FEATURES' => 
  array (
  ),
  'DEL' => 'N',
);

        $id = $this->getIblockPropIdByCode('test2', 1);
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
