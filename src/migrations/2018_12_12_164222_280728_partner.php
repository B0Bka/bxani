<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class Partner20181212164222280728 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
       
    public function up()
    {
        $group = new \CGroup;
        $arFields = Array(
            "ACTIVE"       => "Y",
            "C_SORT"       => 200,
            "NAME"         => "Партнеры",
            "DESCRIPTION"  => "В2В",
            "STRING_ID"      => "partners"
        );
        $NEW_GROUP_ID = $group->Add($arFields);
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function down()
    {
        $filter = Array
        (
            "STRING_ID" => "partners",
        );
        $rsGroups = \CGroup::GetList(($by="c_sort"), ($order="desc"), $filter); // выбираем группы
        while($rsGroups->NavNext(true, "f_")) :
            $id = $f_ID;
        endwhile;

        if (!$id) {
            throw new MigrationException('Не найдено группа для удаления');
        }

        $group = new \CGroup;
        $group->Delete($id);
    }
}
