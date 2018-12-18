<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();
?>
<div class="bx-auth-reg">

    <?if($USER->IsAuthorized()):?>

        <p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

    <?else:?>
        <form class="register_form_partner" method="post" name="regform" enctype="multipart/form-data" data-type="<?=$arParams['TYPE']?>">
            <?foreach($arResult['FIELDS'] as $field):
                if($field['TYPE'] == 'list'):?>
                    <select name="<?=$field['CODE']?>" class="<?=$field['ADDITIONAL_CLASS']?>"
                        <?foreach($field['LIST'] as $listId => $option):?>
                            <option value="<?=$listId?>"><?=$option?></option>
                        <?endforeach?>
                    </select>
                <?elseif($field['TYPE'] == 'date'):?>
                    <input type="date" name="<?=$field['CODE']?>" class="<?=$field['ADDITIONAL_CLASS']?>"
                        placeholder="<?=i18n('PLACEHOLDER_'.$field['CODE'],'register');?>">
                <?elseif ($field['TYPE'] == 'password'):?>
                    <input type="password" name="<?=$field['CODE']?>" class="<?=$field['ADDITIONAL_CLASS']?>"
                        placeholder="<?=i18n('PLACEHOLDER_'.$field['CODE'],'register');?>*">
                <?elseif($field['CODE'] == 'UF_WHATSAPP'):?>
                    <input type="checkbox" class="whatsapp_checkbox" name="whatsapp_checkbox"/>WhatsApp
                    <input type="text" name="<?=$field['CODE']?>" class="<?=$field['ADDITIONAL_CLASS']?>"
                        placeholder="<?=i18n('PLACEHOLDER_'.$field['CODE'],'register');?>*">
                <?else:?>
                    <input type="text" name="<?=$field['CODE']?>" class="<?=$field['ADDITIONAL_CLASS']?>"
                           placeholder="<?=i18n('PLACEHOLDER_'.$field['CODE'],'register');?><?=!empty($field['REQUIRED']) ? '*' : ''?>">
                <?endif;
            endforeach;?>
            <input type="button" class="submit" name="register_submit_button" value="<?=i18n("REGISTER_BUTTON", "register")?>" />
            <span class="system-error"></span>
        </form>
    <?endif?>
</div>
<script>
$(document).ready(function () {
    registration_<?=$arParams['TYPE']?> = RegistrationPartner;
    registration_<?=$arParams['TYPE']?>.init();
    registration_<?=$arParams['TYPE']?>.setParams('<?=$component->getSignedComponentParams()?>');
});
</script>
<style>
    .whatsapp-input{
        display: none;
    }
</style>