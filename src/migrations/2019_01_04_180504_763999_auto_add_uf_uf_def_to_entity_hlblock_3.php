<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoAddUfUfDefToEntityHlblock320190104180504763999 extends BitrixMigration
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
  'FIELD_NAME' => 'UF_DEF',
  'USER_TYPE_ID' => 'boolean',
  'XML_ID' => '',
  'SORT' => 800,
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 'a:4:{s:13:"DEFAULT_VALUE";i:0;s:7:"DISPLAY";s:8:"CHECKBOX";s:5:"LABEL";a:2:{i:0;N;i:1;N;}s:14:"LABEL_CHECKBOX";N;}',
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'По умолчанию',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'По умолчанию',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'По умолчанию',
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
        $id = $this->getUFIdByCode('HLBLOCK_3', 'UF_DEF');
        if (!$id) {
            throw new MigrationException('Не найдено пользовательское свойство для удаления');
        }

        (new CUserTypeEntity())->delete($id);
    }
}
