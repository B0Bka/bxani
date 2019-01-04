<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoAddIblockElementPropertyBook2ToIb120190104180509680336 extends BitrixMigration
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
  'NAME' => 'Справочник 2',
  'SORT' => 500,
  'CODE' => 'book2',
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
    'TABLE_NAME' => 'b_hlbd_book2',
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
        $id = $this->getIblockPropIdByCode('book2', 1);

        $ibp = new CIBlockProperty();
        $deleted = $ibp->delete($id);

        if (!$deleted) {
            throw new MigrationException('Ошибка при удалении свойства инфоблока '.$ibp->LAST_ERROR);
        }
    }
}
