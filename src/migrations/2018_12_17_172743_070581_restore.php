<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
class Restore20181217172743070581 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    private $arData = [
        [
            'UF_HASH' => 'RESTORE_SUCCESS',
            'UF_MESSAGE' => 'На вашу почту отравлено письмо с ссылкой для смены пароля',
            'UF_GROUP' => 'restore',
        ],
        [
            'UF_HASH' => 'RESTORE_ERROR',
            'UF_MESSAGE' => 'Введенный email не найден',
            'UF_GROUP' => 'restore',
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
