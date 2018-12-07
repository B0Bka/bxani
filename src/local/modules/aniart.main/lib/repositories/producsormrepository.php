<?

namespace Aniart\Main\Repositories;

class ProductsOrmRepository extends AbstractOrmRepository
{
    public function newInstance(array $fields = array())
    {
        return app('Product', array($fields));
    }
}
