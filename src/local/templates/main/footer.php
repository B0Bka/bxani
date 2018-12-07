<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<?if($APPLICATION->GetDirProperty('static') === 'Y'):?>
</div>
<?endif;?>

</div>
</main>
<!-- Конец Основная часть -->
        
<!-- Подписка -->
<?if($mainDetect):?>
<?$APPLICATION->IncludeComponent(
    'aniart:subscription',
    'main',
    [
        'ID' => [SUB_DISCOUNT],
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => 3600000
    ]
);?>
<?endif;?>
<!-- Конец Подписка -->
        
<!-- Подвал -->
<footer>
    <div class="container">
        <div class="footer-menu">
            <?$APPLICATION->IncludeComponent(
                "bitrix:menu",
                "service",
                array(
                    "ROOT_MENU_TYPE" => "m_service",
                    "MENU_CACHE_TYPE" => "A",
                    "MENU_CACHE_TIME" => "360000",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS" => array(""),
                    "MAX_LEVEL" => "1",
                    "CHILD_MENU_TYPE" => "",
                    "USE_EXT" => "N",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N"
                )
            );?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:menu",
                "company",
                array(
                    "ROOT_MENU_TYPE" => "m_company",
                    "MENU_CACHE_TYPE" => "A",
                    "MENU_CACHE_TIME" => "360000",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS" => array(""),
                    "MAX_LEVEL" => "1",
                    "CHILD_MENU_TYPE" => "",
                    "USE_EXT" => "N",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N"
                )
            );?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:menu",
                "personal",
                array(
                    "ROOT_MENU_TYPE" => "m_personal",
                    "MENU_CACHE_TYPE" => "A",
                    "MENU_CACHE_TIME" => "360000",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS" => array(""),
                    "MAX_LEVEL" => "1",
                    "CHILD_MENU_TYPE" => "",
                    "USE_EXT" => "N",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N"
                )
            );?>
            <div class="foot-soc">
                <?$APPLICATION->IncludeComponent(
                    'bitrix:main.include', '', 
                    array(
                        'AREA_FILE_SHOW' => 'file', 
                        'PATH' => SITE_TEMPLATE_PATH.'/include/footer_soc.php'
                    ), 
                    false
                );?>
            </div>
        </div>
        <div class="footer-shop">
            <div class="footer-tit">
                <a href="/our-shops">Наши магазины</a>
            </div>
            <div class="footer-shop-thumb">
                <a href="/our-shops">
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/shop.jpg" alt=" " />
                </a>
            </div>
            <div class="shop-map">
                <a href="#">
                    Посмотреть карту
                </a>
            </div>
        </div>
    </div>
    <div class="foot-bot">
        <div class="container">
            <div class="copy">
                © 2018 NATALI BOLGAR
            </div>
            <div class="dev">
                <a href="http://www.aniart.com.ua/">
                    Сайт создан в AniArt
                </a>
            </div>
        </div>
    </div>
</footer>
</div>

<?
include 'include/modals.php';

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/lib/bootstrap.min.js');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/init.js');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/app.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/auth.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module//subscription.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/catalog.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/personal.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/basket.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/reorder.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/shops.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/final.js');

?>

</body>
                                  
</html>
