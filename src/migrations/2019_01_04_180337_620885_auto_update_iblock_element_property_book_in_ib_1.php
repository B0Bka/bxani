<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoUpdateIblockElementPropertyBookInIb120190104180337620885 extends BitrixMigration
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
  'ID' => 7,
  'NAME' => 'Справочник',
  'SORT' => 500,
  'CODE' => 'book',
  'MULTIPLE' => 'N',
  'IS_REQUIRED' => 'N',
  'ACTIVE' => 'Y',
  'USER_TYPE' => 'directory',
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
    'TABLE_NAME' => 'color',
    'LANG' => 
    array (
      'UF_NAME' => 'Название',
      'UF_SORT' => 'Сортировка',
      'UF_XML_ID' => 'Внешний код',
      'UF_FILE' => 'Изображение',
      'UF_LINK' => 'Ссылка',
      'UF_DEF' => 'По умолчанию',
      'UF_DESCRIPTION' => 'Описание',
      'UF_FULL_DESCRIPTION' => 'Полное описание',
    ),
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
    'iblock:LIST_PAGE_SHOW' => 
    array (
      'ID' => '11',
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'LIST_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
    'iblock:DETAIL_PAGE_SHOW' => 
    array (
      'ID' => '12',
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
  ),
  'DEL' => 'N',
);

        $id = $this->getIblockPropIdByCode('book', 1);
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
