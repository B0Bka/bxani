<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoAddUfUfFileToEntityHlblock320190104180505050119 extends BitrixMigration
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
  'ENTITY_ID' => 'HLBLOCK_3',
  'FIELD_NAME' => 'UF_FILE',
  'USER_TYPE_ID' => 'file',
  'XML_ID' => '',
  'SORT' => 900,
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 'a:6:{s:4:"SIZE";i:20;s:10:"LIST_WIDTH";i:0;s:11:"LIST_HEIGHT";i:0;s:13:"MAX_SHOW_SIZE";i:0;s:16:"MAX_ALLOWED_SIZE";i:0;s:10:"EXTENSIONS";a:0:{}}',
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'Изображение',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'Изображение',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'Изображение',
  ),
);

        $this->addUF($fields);
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function down()
    {
        $id = $this->getUFIdByCode('HLBLOCK_3', 'UF_FILE');
        if (!$id) {
            throw new MigrationException('Не найдено пользовательское свойство для удаления');
        }

        (new CUserTypeEntity())->delete($id);
    }
}
