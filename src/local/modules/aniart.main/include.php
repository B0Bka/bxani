<?php

use Aniart\Main\Multilang\I18n;
use Aniart\Main\Multilang\Models\Lang;
use Aniart\Main\Multilang\Models\LangsList;
use Aniart\Main\Multilang\Repositories\HLCMessagesRepository;
use Aniart\Main\Seo\CustomFilterSEFController;
use Aniart\Main\ServiceLocator;
use Aniart\Main\View;

$modulePath = dirname(__FILE__);

include $modulePath.'/lib/dBug.php';

include $modulePath . '/vars.php';
include $modulePath . '/utils.php';
include $modulePath . '/misc.php';
include $modulePath . '/events.php';

Bitrix\Main\Loader::includeModule('iblock');
Bitrix\Main\Loader::includeModule('highloadblock');
Bitrix\Main\Loader::includeModule('catalog');
Bitrix\Main\Loader::includeModule('sale');

include_once $_SERVER['DOCUMENT_ROOT'].'/.composer/vendor/autoload.php';

Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    '\Aniart\Main\Ajax\Handlers\OrderAjaxHandler' => '/local/components/aniart/sale.order/ajax.php',
    '\Aniart\Main\Ajax\Handlers\ProductsListAjaxHandler' => '/local/components/aniart/products.list/ajax.php',
]);

$langs = new LangsList([
    new Lang('ru', 'Русский', ['iso' => 'ru']),
    new Lang('ua', 'Украинский', ['iso' => 'ua']),
], 'ru');

app()->bind([
    'CacheCell' => '\Aniart\Main\Cacher\BXCacheCell',
    'logger' => '\Aniart\Main\Logger',
    'Basket' => 'Aniart\Main\Models\Basket',
    'BasketItem' => 'Aniart\Main\Models\BasketItem',
    'Product' => 'Aniart\Main\Models\Product',
    'ProductSection' => 'Aniart\Main\Models\ProductSection',
    'Order' => '\Aniart\Main\Models\Order',
    'SaleDelivery' => 'Aniart\Main\Models\SaleDelivery',
    'SaleDeliveryService' => 'Aniart\Main\Models\SaleDeliveryService',
    'SalePaySystem' => 'Aniart\Main\Models\SalePaySystem',
    'Store' => '\Aniart\Main\Models\Store',
]);

app()->singleton([
    'LangMessagesRepository' => function() use ($langs){
        return new HLCMessagesRepository(HL_LANG_MESSAGES_ID, $langs);
    },
    'I18n' => function(ServiceLocator $locator) use ($langs){
        return new I18n(
            $locator->make('LangMessagesRepository'),
            $langs,
            'code'
        );
    },
    'ProductsRepository' => function(ServiceLocator $locator){
        return new \Aniart\Main\Repositories\ProductsRepository(PRODUCTS_IBLOCK_ID);
    },
    'ProductSectionsRepository' => function(ServiceLocator $locator){
        return new \Aniart\Main\Repositories\ProductSectionsRepository(PRODUCTS_IBLOCK_ID);
    },
    'BasketItemsRepository' => function(ServiceLocator $locator){
        return new \Aniart\Main\Repositories\BasketItemsRepository(app('BasketItem', [[]]));
    },
    'SaleOrdersRepository' => function(ServiceLocator $locator){
        return new \Aniart\Main\Repositories\SaleOrdersRepository(app('Order',[[]]));
    },
    'PaySystemsRepository' => function(ServiceLocator $locator){
        return new \Aniart\Main\Repositories\SalePaySystemsRepository(
            $locator->make('SalePaySystem', [[]])
        );
    },
    'DeliveriesRepository' => function(ServiceLocator $locator){
        return new Aniart\Main\Repositories\SaleDeliveriesRepository(
            $locator->make('SaleDelivery', [[]])
        );
    },
    'DeliveryServicesRepository' => 'Aniart\Main\Repositories\SaleDeliveryServicesRepository',
    'StoresRepository' => 'Aniart\Main\Repositories\StoresRepository',
    'StoresProductRepository' => Aniart\Main\Repositories\StoresProductRepository::class,
    'SeoParamsCollector' => '\Aniart\Main\Seo\SeoParamsCollector',
    'IBlockTools' => function (ServiceLocator $locator) {
        return new \Aniart\Main\Tools\IBlock();
    },
    'FilterProperty' => function(ServiceLocator $locator){
        return new \Aniart\Main\Tools\FilterProperty(PRODUCTS_IBLOCK_ID, OFFERS_IBLOCK_ID);
    },
    'CatalogService' => \Aniart\Main\Services\Catalog\Service::class,
    'BasketService' => '\Aniart\Main\Services\BasketService',
    'RedirectService' => '\Aniart\Main\Services\RedirectService',
    'PromotionsRepository' => function(ServiceLocator $locator){
        return new \Aniart\Main\Repositories\PromotionsRepository(PROMOTIONS_IBLOCK_ID);
    },
    'ProductsOrmRepository' => function(ServiceLocator $locator){
        return new \Aniart\Main\Repositories\ProductsOrmRepository();
    },
]);
    
//Доп параметры для чпу-фильтров
CustomFilterSEFController::setAdditionalFilteredProps(['sizes']);
\Aniart\Main\Ajax\AjaxHandlerFactory::init([
	'common' => '\Aniart\Main\Ajax\Handlers\CommonAjaxHandler',
    'auth' => '\Aniart\Main\Ajax\Handlers\AuthAjaxHandler',
    'catalog' => '\Aniart\Main\Ajax\Handlers\CatalogAjaxHandler',
    'subscribe' => '\Aniart\Main\Ajax\Handlers\SubscribeAjaxHandler',
    'basket' => '\Aniart\Main\Ajax\Handlers\BasketAjaxHandler',
    'order' => '\Aniart\Main\Ajax\Handlers\OrderAjaxHandler',
    'favorites' => '\Aniart\Main\Ajax\Handlers\FavoritesAjaxHandler',
    'products.list' => \Aniart\Main\Ajax\Handlers\ProductsListAjaxHandler::class,
    'stores.product.list' => \Aniart\Main\Ajax\Handlers\StoreProductListAjaxHandler::class,
]);

$jsExtConfig = [
    'jquery_1' => [
        'js' => '/local/modules/aniart.main/js/jquery-1.10.2.min.js'
    ]
];
foreach($jsExtConfig as $extName => $extParams)
{
    \CJSCore::RegisterExt($extName, $extParams);
}

\CJSCore::Init(['jquery_1']);
