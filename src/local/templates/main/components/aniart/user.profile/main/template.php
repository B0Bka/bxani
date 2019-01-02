<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if($USER->IsAuthorized()):?>
    <form class="profile_form" method="post" name="regform" enctype="multipart/form-data">
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
    <script type="text/javascript">
        $(document).ready(function(){
            UserProfile.init();
            UserProfile.setParams('<?=json_encode($arParams)?>');
        });
    </script>
<?else:?>

<div class="lk-info-top">
    <a class="bt-1" data-toggle="modal" data-target="#myModal">Войти в личный кабинет</a>
</div>

<?endif;?>
