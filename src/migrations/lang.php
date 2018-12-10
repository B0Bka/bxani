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
