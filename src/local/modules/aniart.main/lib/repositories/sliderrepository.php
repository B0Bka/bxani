<?php

namespace Aniart\Main\Repositories;

use Aniart\Main\Models\Slider;

class SliderRepository extends AbstractIblockElementRepository
{
    public function newInstance(array $fields = [])
    {
        return new Slider($fields);
    }

}