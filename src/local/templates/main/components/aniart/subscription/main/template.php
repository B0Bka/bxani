<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

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
            <?=i18n('SUBSCRIPTION_TITLE', 'subscription', '')?>
            <p><?=i18n('SUBSCRIPTION_DESC', 'subscription', '')?></p>
        </div>
        <div class="form-feed">
            <div class="feed-mail-inp">
                <input name="SUB-EMAIL" type="text" data-req="1" placeholder="<?=i18n('SUBSCRIPTION_INPUT', 'subscription', 'ru')?>">
            </div>
            <div class="feed-mail-name">
                <input id="sub_add" type="button" value="<?=i18n('SUBSCRIPTION_BUTTON', 'subscription', 'ru')?>">
            </div>
        </div>
        </form>
    </div>
</div>