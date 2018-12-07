<?
namespace Aniart\Main\Repositories;

use Aniart\Main\Models\Shop;

class ShopsRepository extends AbstractHLBlockElementsRepository
{

    public function newInstance(array $fields)
    {
        return new Shop($fields);
    }
}