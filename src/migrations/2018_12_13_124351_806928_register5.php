<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
class Register520181213124351806928 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    private $arData = [
        [
            'UF_HASH' => 'ERROR_EMPTY_PERSONAL_PHONE',
            'UF_MESSAGE' => 'Заполните поле Телефон',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_PERSONAL_CITY',
            'UF_MESSAGE' => 'Заполните поле Город',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_PERSONAL_MOBILE',
            'UF_MESSAGE' => '	Заполните второй номер телефона',
            'UF_GROUP' => 'register',
        ],[
            'UF_HASH' => 'ERROR_EMPTY_WORK_COMPANY',
            'UF_MESSAGE' => 'Заполните поле Название компании',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_WORK_POSITION',
            'UF_MESSAGE' => 'Заполните поле Должность',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'EMAIL_EXISTS',
            'UF_MESSAGE' => 'Вы уже зарегистрированы на сайте',
            'UF_GROUP' => 'register',
        ],
    ];

    public function up()
    {
        \CModule::IncludeModule('highloadblock');
        $entity_data_class = $this->GetEntityDataClass(HL_LANG_MESSAGES_ID);
        foreach($this->arData as $word)
        {
            if(!$this->wordExist($word['UF_HASH'], $entity_data_class))
                $result = $entity_data_class::add($word);
        }
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function down()
    {
        //
    }

    private function GetEntityDataClass($HlBlockId) {
        if (empty($HlBlockId) || $HlBlockId < 1)
        {
            return false;
        }
        $hlblock = HLBT::getById($HlBlockId)->fetch();
        $entity = HLBT::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }
    private function wordExist($hash, $entity)
    {
        return $entity::getCount(['UF_HASH'=> $hash]);
    }
}
