<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//new dBug($arParams, '', true);
//new dBug($arResult, '', true);
?>

<div class="feed-mail">
    <div class="container">
        <div id="sub_msg" style="text-transform:none;"></div>
        <form 
            id="sub_form" 
            method="post" 
            action="javascript:void(0);" 
            enctype="multipart/form-data"
        >
        <div class="feed-mail-name">
            Хочешь быть в тренде?
        </div>
        <div class="form-feed">
            <div class="feed-mail-inp">
                <input name="SUB-EMAIL" type="text" data-req="1" placeholder="твой e-mail">
            </div>
            <div class="feed-mail-name">
                <input id="sub_add" type="button" value="подписаться на рассылку">
            </div>
        </div>
        </form>
    </div>
</div>