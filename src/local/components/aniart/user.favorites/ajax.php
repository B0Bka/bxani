<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 7/26/2017
 * Time: 4:59 PM
 */
use \Aniart\Main\FavoritesTable;

define("NO_KEEP_STATISTIC", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = $_REQUEST;
/*
 * если получили данные по аяксу
 */
if($request['ajax_mode_delete'] == 'Y')
{
    $APPLICATION->RestartBuffer();

    $prodId = $request['id_element_delete'];
    global $USER;


    $userId = $USER->GetID();
    $isDelete = false;

    if($prodId != 0 && $userId > 0 ){
        /*
         *  удаляем элемент с таблицы
         */
        $rsFav = FavoritesTable::getList(array(
            'select' => array('ID'),
            'filter' => array('=USER_ID' => $userId, '=PRODUCT_ID' => $prodId)
        ));
        if($fav = $rsFav->fetch()){

            $result = FavoritesTable::delete($fav['ID']);
            $isDelete = $result->isSuccess();

        }
        /*
         *  если все хорошо, получаем текущие элементы с таблицы и отправлем их на компонент
         */
        if($isDelete){

            $arElementIDs = FavoritesTable::GetFavoritesProductsIDs($USER->GetID());

            if(count($arElementIDs) > 0){
                $GLOBALS['arrFavoriteFilter'] = array("ID" => $arElementIDs);

                $params = $request['params'];
                /*
                 *  режим аякса включен
                 */
                $params['AJAX_MODE2'] = 'Y';
                /*
                 *  передаем текущую страницу на которой находимся
                 */
                $params['NAV_NUM'] = $request["current_page"];

                $APPLICATION->IncludeComponent(
                    'aniart:user.favorites',
                    $request['template'],
                    $params
                );
               die();

            }
            else{
                echo "<div>Ваш список желаний пуст</div>";
            }

        } /*
            если удаление неккоректное, передаем просто пустой div, чтобы не висел компонент
          */
        else{
            echo "<div></div>";
        }


    }


   die();

}



?>