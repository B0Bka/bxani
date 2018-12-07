<?

namespace Aniart\Main\Repositories;

class FavoritesRepository extends AbstractIblockElementRepository
{

    public function newInstance(array $fields = array())
    {
        return app('Product', array($fields));
    }

    public function getFav(){
        global $USER;
        return \Aniart\Main\FavoritesTable::getProductIds($USER->GetID());
    }

}