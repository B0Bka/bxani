<?php

//iblock
define('PRODUCTS_IBLOCK_ID', 2);

//prices
define('RETAIL_CODE_PRICE', 'Розничная');
define('RETAIL_ID_PRICE', 5);

//highloadblock
define('HL_LANG_MESSAGES_ID', 1);

//subscription
define('SUB_DISCOUNT', 2);

//Группы свойств заказа OPG = Order Properties Group
define('OPG_USER_INFO', 1);
define('OPG_DELIVERY', 2);

//order delivery
define('NEW_POST_DELIVERY_ID', 3);

//user groups
define('USER_GROUP_ADMIN', 1);
define('USER_GROUP_DEFAULT', 2);

//Письмо для восстановления пароля
define('EVENT_FORGOT_PASSWORD', 'USER_RESTORE_PASS');
define('MESS_FORGOT_PASSWORD', 86);
define('RESTORE_PAGE', '/personal/checkpass.php');
//Время между отправками восстановления пароля
define('RESTORE_PAUSE_SECONDS', 180);

define('USER_FAVORITES_TABLE','user_favorites');
define('USER_ADMIN_ID',1);
define('CONTENT_MANAGER_GROUP',6);

define('GOOGLE_KEY', '929498189152-8c1hlrblgk6cuh1a8fg8982n9jsn12mq.apps.googleusercontent.com');
define('GOOGLE_SECRET', 'sFg8VdUoRCmpOOjsT_7aghrY');
define('FACEBOOK_KEY', '376514502814671');
define('FACEBOOK_SECRET', '716112f91d05c43b1dbf3d4da4ba164e');
define('SOCAUTH_REDIRECT_URI', 'https://natalibolgar.com/personal/auth.php');

$actual_link = "https://$_SERVER[HTTP_HOST]";
define('ACTUAL_LINK', $actual_link);