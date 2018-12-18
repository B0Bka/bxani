<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
class Changepassword20181218122204183092 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    private $arData = [
        [
            'UF_HASH' => 'CHANGE_PASSWORD',
            'UF_MESSAGE' => 'Восстановление пароля',
            'UF_GROUP' => 'change',
        ],
        [
            'UF_HASH' => 'CHANGE_PASSWORD_BUTTON',
            'UF_MESSAGE' => 'Сменить пароль',
            'UF_GROUP' => 'change',
        ],
        [
            'UF_HASH' => 'CHANGE_SUCCESS',
            'UF_MESSAGE' => 'Пароль успешно сменен',
            'UF_GROUP' => 'change',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_LOGIN',
            'UF_MESSAGE' => 'Заполните поле E-mail',
            'UF_GROUP' => 'auth',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_PASSWORD',
            'UF_MESSAGE' => 'Заполните поле Пароль',
            'UF_GROUP' => 'auth',
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
