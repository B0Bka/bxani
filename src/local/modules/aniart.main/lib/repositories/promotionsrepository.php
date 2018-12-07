<?php

namespace Aniart\Main\Repositories;

use Aniart\Main\Models\Promotion;

class PromotionsRepository extends AbstractIblockElementRepository
{
    public function newInstance(array $fields = [])
    {
        return new Promotion($fields);
    }
}