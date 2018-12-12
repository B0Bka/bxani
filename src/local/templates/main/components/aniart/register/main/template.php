<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();
?>
<div class="bx-auth-reg">

    <?if($USER->IsAuthorized()):?>

        <p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

    <?else:?>
        <form class="register_form" method="post" name="regform" enctype="multipart/form-data">
            <input type="hidden" name="client" value="partner"/>
            <input type="hidden" name="req" value="<?=implode(';', $arResult['ALL_FIELDS_CODE'])?>">
            <?foreach($arResult['FIELDS'] as $field):
                if($field['TYPE'] == 'list'):?>
                    <select name="<?=$field['CODE']?>">
                        <?foreach($field['LIST'] as $listId => $option):?>
                            <option value="<?=$listId?>"><?=$option?></option>
                        <?endforeach?>
                    </select>
                <?elseif($field['TYPE'] == 'date'):?>
                    <input type="date" name="<?=$field['CODE']?>" placeholder="<?=i18n('PLACEHOLDER_'.$field['CODE'],'register');?>">
                <?elseif ($field['TYPE'] == 'password'):?>
                    <input type="password" name="<?=$field['CODE']?>" placeholder="<?=i18n('PLACEHOLDER_'.$field['CODE'],'register');?>">
                <?else:?>
                    <input type="text" name="<?=$field['CODE']?>" placeholder="<?=i18n('PLACEHOLDER_'.$field['CODE'],'register');?>">
                <?endif;
            endforeach;?>
            <input type="button" class="submit" name="register_submit_button" value="qq<?=GetMessage("AUTH_REGISTER")?>" />
            <span class="system-error"></span>
        </form>
    <?endif?>
</div>