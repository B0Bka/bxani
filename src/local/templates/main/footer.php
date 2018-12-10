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
<?$APPLICATION->IncludeComponent(
    'aniart:subscription',
    'main',
    [
        'ID' => [SUB_DISCOUNT],
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => 3600000
    ]
);?>
<!-- Конец Подписка -->
        
<!-- Подвал -->
<footer>
    <div class="container">
        <? $APPLICATION->IncludeComponent(
            'bitrix:main.include', '',
            array(
                'AREA_FILE_SHOW' => 'file',
                'PATH' => SITE_TEMPLATE_PATH . '/include/footer_address.php'
            ),
            false
        ); ?>
        <? $APPLICATION->IncludeComponent(
            'bitrix:main.include', '',
            array(
                'AREA_FILE_SHOW' => 'file',
                'PATH' => SITE_TEMPLATE_PATH . '/include/schedule.php'
            ),
            false
        ); ?>
        <? $APPLICATION->IncludeComponent(
            'bitrix:main.include', '',
            array(
                'AREA_FILE_SHOW' => 'file',
                'PATH' => SITE_TEMPLATE_PATH . '/include/phone_1.php'
            ),
            false
        ); ?>
        <? $APPLICATION->IncludeComponent(
            'bitrix:main.include', '',
            array(
                'AREA_FILE_SHOW' => 'file',
                'PATH' => SITE_TEMPLATE_PATH . '/include/phone_2.php'
            ),
            false
        ); ?>

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
        </div>
    </div>
    <div class="foot-bot">
        <div class="container">
            <div class="copy">
                <?$APPLICATION->IncludeComponent(
                    'bitrix:main.include', '',
                    array(
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_TEMPLATE_PATH.'/include/copyright.php'
                    ),
                    false
                );?>
                <?$APPLICATION->IncludeComponent(
                    'bitrix:main.include', '',
                    array(
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_TEMPLATE_PATH.'/include/oferta.php'
                    ),
                    false
                );?>
                <?$APPLICATION->IncludeComponent(
                    'bitrix:main.include', '',
                    array(
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_TEMPLATE_PATH.'/include/email.php'
                    ),
                    false
                );?>
                <?$APPLICATION->IncludeComponent(
                    'bitrix:main.include', '',
                    array(
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_TEMPLATE_PATH.'/include/socials.php'
                    ),
                    false
                );?>
            </div>
        </div>
    </div>
</footer>
</div>

<?
//include 'include/modals.php';

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/lib/bootstrap.min.js');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/init.js');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/app.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/auth.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/subscription.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/catalog.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/personal.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/module/basket.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/src/final.js');

?>

</body>
                                  
</html>
