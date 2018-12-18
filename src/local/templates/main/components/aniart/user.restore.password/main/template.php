<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<form class="restore_form" method="post" name="regform" enctype="multipart/form-data">
    <input type="text" name="EMAIL" placeholder="<?=i18n('RESTORE_PLACEHOLDER', 'restore')?>">
    <input type="button" class="submit" name="forgot-submit" value="<?=i18n('RESTORE_BUTTON', 'restore')?>">
</form>
<span class="restoreSuccess"></span>
<script>
    $(document).ready(function () {
        restoreForm = RestorePassword;
        restoreForm.init();
    });
</script>