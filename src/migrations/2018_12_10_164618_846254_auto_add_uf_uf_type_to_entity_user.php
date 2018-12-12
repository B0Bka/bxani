<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoAddUfUfTypeToEntityUser20181210164618846254 extends BitrixMigration
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
  'ENTITY_ID' => 'USER',
  'FIELD_NAME' => 'UF_TYPE',
  'USER_TYPE_ID' => 'enumeration',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 'a:4:{s:7:"DISPLAY";s:4:"LIST";s:11:"LIST_HEIGHT";i:5;s:16:"CAPTION_NO_VALUE";s:0:"";s:13:"SHOW_NO_VALUE";s:1:"Y";}',
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'Тип',
    'en' => 'Тип',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'Тип',
    'en' => 'Тип',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'Тип',
    'en' => 'Тип',
  ),
  'ERROR_MESSAGE' => 
  array (
    'ru' => '',
    'en' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'ru' => '',
    'en' => '',
  ),
);
        $enumList = ['Физическое лицо', 'Юридическое лицо'];
        foreach ($enumList as $i => $enum)
        {
            $arAddEnum['n'.$i] = array(
                'VALUE' => $enum,//значение
                'DEF' => 'N',//по умолчанию
                'SORT' => $i*10//сортировка
            );
        }
        $id = $this->addUF($fields);
        $obEnum = new \CUserFieldEnum();
        $obEnum->SetEnumValues($id, $arAddEnum);
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function down()
    {
        $id = $this->getUFIdByCode('USER', 'UF_TYPE');
        if (!$id) {
            throw new MigrationException('Не найдено пользовательское свойство для удаления');
        }

        (new CUserTypeEntity())->delete($id);
    }
}
