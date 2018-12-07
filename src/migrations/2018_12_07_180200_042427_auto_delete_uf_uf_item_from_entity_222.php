<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoDeleteUfUfItemFromEntity22220181207180200042427 extends BitrixMigration
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
  'ID' => '17',
  'ENTITY_ID' => '222',
  'FIELD_NAME' => 'UF_ITEM',
  'USER_TYPE_ID' => 'mail_message',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => NULL,
);
        $id = $this->getUFIdByCode('222', 'UF_ITEM');

        $oUserTypeEntity = new CUserTypeEntity();

        $dbResult = $oUserTypeEntity->delete($id);
        if (!$dbResult->result) {
            throw new MigrationException("Не удалось обновить удалить свойство с FIELD_NAME = {$fields['FIELD_NAME']} и ENTITY_ID = {$fields['ENTITY_ID']}");
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
        return false;
    }
}
