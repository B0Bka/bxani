<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<form class="change_form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="LOGIN" value="<?=htmlspecialchars($_REQUEST['LOGIN'])?>">
    <input type="hidden" name="CHECKWORD" value="<?=htmlspecialchars($_REQUEST['CHECKWORD'])?>">
    <input type="text" name="PASSWORD" placeholder="<?=i18n('PLACEHOLDER_PASSWORD', 'register')?>">
    <input type="text" name="CONFIRM_PASSWORD" placeholder="<?=i18n('PLACEHOLDER_CONFIRM_PASSWORD', 'register')?>">
    <input type="button" class="submit" name="change-submit" value="<?=i18n('CHANGE_PASSWORD_BUTTON', 'change')?>">
</form>
<span class="restoreSuccess"></span>
<script>
    $(document).ready(function () {
        changeForm = ChangePassword;
        changeForm.init();
    });
</script>