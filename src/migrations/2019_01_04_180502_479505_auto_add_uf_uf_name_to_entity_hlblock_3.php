<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoAddUfUfNameToEntityHlblock320190104180502479505 extends BitrixMigration
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
  'FIELD_NAME' => 'UF_NAME',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => 200,
  'MULTIPLE' => 'N',
  'MANDATORY' => 'Y',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 'a:6:{s:4:"SIZE";i:20;s:4:"ROWS";i:1;s:6:"REGEXP";N;s:10:"MIN_LENGTH";i:0;s:10:"MAX_LENGTH";i:0;s:13:"DEFAULT_VALUE";N;}',
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'Название',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'Название',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'Название',
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
        $id = $this->getUFIdByCode('HLBLOCK_3', 'UF_NAME');
        if (!$id) {
            throw new MigrationException('Не найдено пользовательское свойство для удаления');
        }

        (new CUserTypeEntity())->delete($id);
    }
}
