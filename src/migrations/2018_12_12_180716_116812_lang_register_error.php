<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
class LangRegisterError20181212180716116812 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    private $arData = [
        [
            'UF_HASH' => 'SUBSCRIPTION_TITLE',
            'UF_MESSAGE' => 'Будьте в курсе всех последних акций',
            'UF_GROUP' => 'subscription',
        ],
        [
            'UF_HASH' => 'SUBSCRIPTION_INPUT',
            'UF_MESSAGE' => 'Введите ваш e-mail',
            'UF_GROUP' => 'subscription',
        ],
        [
            'UF_HASH' => 'SUBSCRIPTION_DESC',
            'UF_MESSAGE' => 'Подпишитесь на рассылку',
            'UF_GROUP' => 'subscription',
        ],
        [
            'UF_HASH' => 'SUBSCRIPTION_BUTTON',
            'UF_MESSAGE' => 'Подписка на акции',
            'UF_GROUP' => 'subscription',
        ],
        [
            'UF_HASH' => 'SEARCH_PLACEHOLDER',
            'UF_MESSAGE' => 'Поиск по сайту',
            'UF_GROUP' => '',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_NAME',
            'UF_MESSAGE' => 'Имя',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_LAST_NAME',
            'UF_MESSAGE' => 'Фамилия',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_PERSONAL_PHONE',
            'UF_MESSAGE' => 'Номер телефона',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_UF_WHATSAPP',
            'UF_MESSAGE' => 'Номер телефона WhatsApp',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_EMAIL',
            'UF_MESSAGE' => 'E-mail',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_PERSONAL_CITY',
            'UF_MESSAGE' => 'Город',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_WORK_COMPANY',
            'UF_MESSAGE' => 'Название компании',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_WORK_POSITION',
            'UF_MESSAGE' => 'Должность',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_UF_VOEN',
            'UF_MESSAGE' => 'VOEN',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_PERSONAL_BIRTHDAY',
            'UF_MESSAGE' => 'Дата рождения',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_PASSWORD',
            'UF_MESSAGE' => 'Пароль',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_CONFIRM_PASSWORD',
            'UF_MESSAGE' => 'Подтверждение пароля',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'PLACEHOLDER_PERSONAL_MOBILE',
            'UF_MESSAGE' => 'Второй номер телефона',
            'UF_GROUP' => 'register',
        ],

        [
            'UF_HASH' => 'ERROR_EMPTY_EMAIL',
            'UF_MESSAGE' => 'Заполните поле E-mail',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_NAME',
            'UF_MESSAGE' => 'Заполните поле Имя',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_LAST_NAME',
            'UF_MESSAGE' => 'Заполните поле Фамилия',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_PASSWORD',
            'UF_MESSAGE' => 'Заполните поле Пароль',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_CONFIRM_PASSWORD',
            'UF_MESSAGE' => 'Заполните поле Подтверждение пароля',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_PHONE',
            'UF_MESSAGE' => 'Неверный формат телефона',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_DIFFRENT_PASSWORD',
            'UF_MESSAGE' => 'Не совпадает подтверждение пароля',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_SHORT_PASSWORD',
            'UF_MESSAGE' => 'Короткий пароль',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_DIFFRENT_PASSWORD',
            'UF_MESSAGE' => 'Не совпадает подтверждение пароля',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMAIL',
            'UF_MESSAGE' => 'Некорректный e-mail',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMAIL_NOT_FOUND',
            'UF_MESSAGE' => 'E-mail не найден',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'ERROR_EMPTY_PHONE',
            'UF_MESSAGE' => 'Заполните поле Телефон',
            'UF_GROUP' => 'register',
        ],
        [
            'UF_HASH' => 'USER_EXIST',
            'UF_MESSAGE' => 'Пользователь с таким e-mail уже существует',
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
