<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();
?>
<div class="bx-auth-reg">

    <?if($USER->IsAuthorized()):?>

        <p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

    <?else:?>
        <form class="auth_form" method="post" name="authform" enctype="multipart/form-data">
            <input type="text" name="LOGIN" placeholder="<?=i18n('PLACEHOLDER_EMAIL','register');?>*">
            <input type="password" name="PASSWORD" placeholder="<?=i18n('PLACEHOLDER_PASSWORD','register');?>*">
            <input type="button" class="submit" name="auth_submit_button" value="<?=i18n("AUTH_BUTTON", "auth")?>" />
            <span class="system-error"></span>
        </form>
    <?endif?>
</div>
<script>
$(document).ready(function () {
    authForm = Auth;
    authForm.init();
});
</script>