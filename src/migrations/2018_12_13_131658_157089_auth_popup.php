<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
class AuthPopup20181213131658157089 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    private $arData = [
        [
            'UF_HASH' => 'REGISTER_BUTTON',
            'UF_MESSAGE' => 'Регистрация',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'AUTH_TITLE',
            'UF_MESSAGE' => 'Авторизация',
            'UF_GROUP' => 'auth',
        ],
        [
            'UF_HASH' => 'AUTH_TAB',
            'UF_MESSAGE' => 'Вход в кабинет',
            'UF_GROUP' => 'auth',
        ],
        [
            'UF_HASH' => 'REGISTER_TAB',
            'UF_MESSAGE' => 'Регистрация',
            'UF_GROUP' => 'auth',
        ],
        [
            'UF_HASH' => 'CLIENT_TAB',
            'UF_MESSAGE' => 'Покупатель',
            'UF_GROUP' => 'auth',
        ],
        [
            'UF_HASH' => 'PARTNER_TAB',
            'UF_MESSAGE' => 'Партнер',
            'UF_GROUP' => 'auth',
        ],
        [
            'UF_HASH' => 'AUTH_BUTTON',
            'UF_MESSAGE' => 'Войти',
            'UF_GROUP' => 'auth',
        ],
        [
            'UF_HASH' => 'FORGOT_BUTTON',
            'UF_MESSAGE' => 'Забыли пароль',
            'UF_GROUP' => 'auth',
        ],
        [
            'UF_HASH' => 'REGISTER_SOC',
            'UF_MESSAGE' => 'Регистрация через',
            'UF_GROUP' => 'auth',
        ],
        [
            'UF_HASH' => 'AUTH_SOC',
            'UF_MESSAGE' => 'Авторизация через',
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
