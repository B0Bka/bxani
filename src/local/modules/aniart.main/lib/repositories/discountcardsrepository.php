<?
namespace Aniart\Main\Repositories;

use Aniart\Main\Models\DiscountCard;

class DiscountCardsRepository extends AbstractHLBlockElementsRepository
{

    public function newInstance(array $fields)
    {
        return new DiscountCard($fields);
    }

    public function getByCode($code)
    {
        return $this->getList([], ['UF_CODE' => $code]);
    }
}