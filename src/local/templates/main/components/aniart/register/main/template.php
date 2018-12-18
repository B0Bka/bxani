<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();
?>
<div class="bx-auth-reg">

    <?if($USER->IsAuthorized()):?>

        <p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

    <?else:?>
        <form class="register_form_client" method="post" name="regform" enctype="multipart/form-data">
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
                <?else:?>
                    <input type="text" name="<?=$field['CODE']?>" class="<?=$field['ADDITIONAL_CLASS']?>"
                           placeholder="<?=i18n('PLACEHOLDER_'.$field['CODE'],'register');?><?=!empty($field['REQUIRED']) ? '*' : ''?>">
                <?endif;
            endforeach;?>
            <input type="button" class="submit" name="register_submit_button" value="<?=i18n("REGISTER_BUTTON", "register")?>" />
            <span class="system-error"></span>
        </form>
        <div class="auth-soc">
            <?=i18n("REGISTER_SOC", "auth")?>
            <?
            $APPLICATION->IncludeComponent("aniart:blank", "socserv.auth",
                array(
                    "AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
                    "SUFFIX"=>"form",
                ),
                $component,
                array("HIDE_ICONS"=>"Y")
            );
            ?>
        </div>
    <?endif?>
</div>
<script>
$(document).ready(function () {
    registration_<?=$arParams['TYPE']?> = Registration;
    registration_<?=$arParams['TYPE']?>.init();
    registration_<?=$arParams['TYPE']?>.setParams('<?=$component->getSignedComponentParams()?>');
});
</script>