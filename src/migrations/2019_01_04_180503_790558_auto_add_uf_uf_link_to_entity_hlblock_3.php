<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoAddUfUfLinkToEntityHlblock320190104180503790558 extends BitrixMigration
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
  'FIELD_NAME' => 'UF_LINK',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => 500,
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 'a:6:{s:4:"SIZE";i:20;s:4:"ROWS";i:1;s:6:"REGEXP";N;s:10:"MIN_LENGTH";i:0;s:10:"MAX_LENGTH";i:0;s:13:"DEFAULT_VALUE";N;}',
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'Ссылка',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'Ссылка',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'Ссылка',
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
        $id = $this->getUFIdByCode('HLBLOCK_3', 'UF_LINK');
        if (!$id) {
            throw new MigrationException('Не найдено пользовательское свойство для удаления');
        }

        (new CUserTypeEntity())->delete($id);
    }
}
