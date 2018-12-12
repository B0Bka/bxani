<?php

use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

$arData = [
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


];
CModule::IncludeModule('highloadblock');
$entity_data_class = GetEntityDataClass(HL_LANG_MESSAGES_ID);
foreach($arData as $word)
{
    if(!wordExist($word['UF_HASH'], $entity_data_class)) $result = $entity_data_class::add($word);
}

function GetEntityDataClass($HlBlockId) {
    if (empty($HlBlockId) || $HlBlockId < 1)
    {
        return false;
    }
    $hlblock = HLBT::getById($HlBlockId)->fetch();
    $entity = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    return $entity_data_class;
}
function wordExist($hash, $entity)
{
    return $entity::getCount(['UF_HASH'=> $hash]);
}
