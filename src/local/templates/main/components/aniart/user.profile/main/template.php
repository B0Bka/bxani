<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$this->setFrameMode(true);
//new \dBug($arParams);
//new \dBug($arResult, '', true);
?>

<!-- Заголовок для моб -->
<div class="lk-tit">
    <?=i18n('PERSONAL_DATA')?>
</div>
<!-- Заголовок для моб -->

<?if($USER->IsAuthorized()):?>
<form
    id="user_profile" 
    method="POST" 
    action="javascript:void(0);" 
    role="form" 
    enctype="multipart/form-data"
>

<div class="lk-info-top">
    <div class="one-order-row">
        <div class="one-order-form">
            <div class="one-order-tit">
                <?=i18n('EMAIL')?>
            </div>
            <input name="PROFILE-EMAIL" type="text" data-req="1" value="<?=$arResult['EMAIL']?>">
        </div>
        <div class="one-order-form">
            <div class="one-order-tit">
                <?=i18n('NAME')?>
            </div>
            <input name="PROFILE-NAME" type="text" data-req="1" value="<?=$arResult['NAME']?>">
        </div>
        <div class="one-order-form">
            <div class="one-order-tit">
                <?=i18n('LAST_NAME')?>
            </div>
            <input name="PROFILE-LAST_NAME" type="text" data-req="1" value="<?=$arResult['LAST_NAME']?>">
        </div>
    </div>
    <div class="one-order-row" id="loader-row">
        <div class="one-order-form">
            <div class="one-order-tit">
                <?=i18n('PHONE')?>
            </div>
            <input name="PROFILE-PHONE" type="text" data-req="1" value="<?=$arResult['PERSONAL_PHONE']?>" placeholder="+38 (___) ___-__-__">
        </div>
        <div class="one-order-form" style="position: relative">
            <div class="one-order-tit">
                Пароль
                <?//=i18n('HOUSE')?>
            </div>
            <input name="PROFILE-PASSWORD" id="password-field" type="password" data-req="0" value="" <?=!empty($arResult['PASSWORD_RECOVERY']) && $arResult['PASSWORD_RECOVERY']['STATUS'] == 'Y' ? "class='input-error'" : ""?>>
            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
            <?if(!empty($arResult['PASSWORD_RECOVERY']) && $arResult['PASSWORD_RECOVERY']['STATUS'] == 'Y'):?>
                <span id="PASSWORD_error" class="error-mes" style="display: inline;"><?=$arResult['PASSWORD_RECOVERY']['TEXT']?></span>
            <?endif;?>
        </div>
        <div class="one-order-form">
            <div class="one-order-tit">
                Повторите пароль
                <?//=i18n('FLAT')?>
            </div>
            <input id="confirm-field" name="PROFILE-CONFIRM_PASSWORD" type="password" data-req="0" value="" <?=!empty($arResult['PASSWORD_RECOVERY']) && $arResult['PASSWORD_RECOVERY']['STATUS'] == 'Y' ? "class='input-error'" : ""?>>
            <span toggle="#confirm-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
        </div>
    </div>
    <div class="one-order-row">
        <div class="one-order-form">
            <div class="one-order-tit">
                <?=i18n('CITY')?>
            </div>
            <input name="PROFILE-PERSONAL_CITY" type="text" data-req="0" value="<?=$arResult['PERSONAL_CITY']?>">
        </div>
        <div class="one-order-form">
            <div class="one-order-tit">
                <?=i18n('STREET')?>
            </div>
            <input name="PROFILE-PERSONAL_STREET" type="text" data-req="0" value="<?=$arResult['PERSONAL_STREET']?>">
        </div>
        <div class="one-order-form">
            <div class="order-form-50">
                <div class="one-order-tit">
                    <?=i18n('HOUSE')?>
                </div>
                <input name="PROFILE-UF_HOUSE" type="text" data-req="0" value="<?=$arResult['UF_HOUSE']?>">
            </div>
            <div class="order-form-50">
                <div class="one-order-tit">
                    <?=i18n('FLAT')?>
                </div>
                <input name="PROFILE-UF_FLAT" type="text" data-req="0" value="<?=$arResult['UF_FLAT']?>">
            </div>
        </div>
    </div>
</div>

<div class="lk-info-data">
    <div class="one-order-tit">
        <?=i18n('DATE_OF_BIRTH')?>
    </div>
    <input 
        type="hidden" 
        name="PROFILE-UF_BIRTHDAY" 
        value="<?=$arResult['PERSONAL_BIRTHDAY']?>" 
        data-req="0"
    />

    <div class="mar">
        <label>
            <input 
                name="PROFILE-UF_MARRIED" 
                type="checkbox" 
                <?=($arResult['UF_MARRIED'] == 1?'checked':'')?>
            />
            <span>
                <?=i18n('MARRIED')?>
            </span>
        </label>
    </div>

</div>

    <?/*
<div class="lk-info-child">
<div id="personal_children">
<?if(empty($arResult['UF_CHILDREN'])):?>
    <!-- ОДин блок -->
    <div class="one-order-form">
        <div class="one-order-tit">
            <?=i18n('CHILDREN')?>
        </div>
        <input name="PROFILE-UF_CHILD_1" type="text" data-req="0" value="">
    </div>
    <!-- Конец ОДин блок -->
<?else:?>
    <?foreach($arResult['UF_CHILDREN'] as $i=>$child):?>
    <!-- ОДин блок -->
    <div class="one-order-form">
        <div class="one-order-tit">
            <?=i18n('CHILDREN')?>
        </div>
        <input name="PROFILE-UF_CHILD_<?=$i+1?>" type="text" data-req="0" value="<?=$child?>">
    </div>
    <!-- Конец ОДин блок -->
    <?endforeach;?>
<?endif;?>
</div>


<!-- ОДин блок -->
<div class="one-order-form">
    <div class="add-inp">
        <a href="javascript:void(0);">
           <?=i18n('ADD')?> <span><?=i18n('CHILD')?></span>
        </a>
    </div>
</div>
<!-- Конец ОДин блок -->


</div>
*/?>
<span id="msg"></span>
<div class="save-lk">
    <div class="save-lk-in">
        <button id="personal_save" class="border-black">
            <?=i18n('SAVE')?>
        </button>
    </div>
</div>
</form>

<?else:?>

<div class="lk-info-top">
    <?if(!empty($arResult['PASSWORD_RECOVERY']) && $arResult['PASSWORD_RECOVERY']['STATUS'] == 'N'):?>
        <p><?=$arResult['PASSWORD_RECOVERY']['TEXT']?></p>
    <?endif;?>
    <a class="bt-1" data-toggle="modal" data-target="#myModal">Войти в личный кабинет</a>
</div>

<?endif;?>
