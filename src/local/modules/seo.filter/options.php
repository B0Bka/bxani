<?if(!defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option,
    Bitrix\Sale\Internals\DiscountTable;

$module_id = basename(__DIR__);

Loc ::loadMessages(__FILE__);

if(Loader::includeModule("catalog"))
{
    $dbIBlock = CIBlock::GetList(array(), array("ACTIVE"=>"Y", "CNT_ACTIVE"=>"Y"), true);
    while($IBlock = $dbIBlock->Fetch())
        $arIBlock[$IBlock["ID"]] = trim($IBlock["NAME"]).' ['.$IBlock["ID"].']';
}
if(!empty(COption::GetOptionString($module_id, 'iblock_catalog')))
{
    $iblock = COption::GetOptionString($module_id, 'iblock_catalog');
    $arProps = \CIBlockSectionPropertyLink::GetArray($iblock);
    foreach ($arProps as $prop)
    {
        if($prop['SMART_FILTER'] == 'Y')
        {
            $res = \CIBlockProperty::GetByID($prop['PROPERTY_ID'], $iblock);
            if($propFields = $res->GetNext())
                $arPropFields[$propFields['ID']] = $propFields['NAME'];
        }
    }
}
if ($APPLICATION->GetGroupRight($module_id) < "R")
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
Loader::includeModule($module_id);

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
$aTabs = [
    array(
        'DIV' => 'main',
        'TAB' => Loc::getMessage($module_id.'TAB_MAIN'),
        'OPTIONS' => array(
            Loc::getMessage($module_id.'CATEGORY_CATALOG'),
            array(
                'iblock_catalog',
                Loc::getMessage($module_id.'IBLOCK_CATALOG'),
                '1',
                array("selectbox", $arIBlock)
            )
        )
    ),
    array(
        "DIV" => "properties",
        "TAB" => Loc::GetMessage("TAB_PROPERTIES"),
        'OPTIONS' => array(
            array(
                'filter_props_link',
                Loc::getMessage($module_id.'PROPS'),
                '1',
                array("multiselectbox", $arPropFields)
            )
        )
    ),
    array(
        "DIV" => "rights",
        "TAB" => Loc::GetMessage("MAIN_TAB_RIGHTS"),
        "ICON" => "ldap_settings",
        "TITLE" => Loc::GetMessage("MAIN_TAB_TITLE_RIGHTS")
    ),
];

if ($request->isPost() && check_bitrix_sessid())
{
    if (strlen($request['save'])>0)
    {
        foreach ( $aTabs as $arTab )
        {
            __AdmSettingsSaveOptions( $module_id, $arTab[ 'OPTIONS' ] );
        }
    }
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>
    <form method='post' action='<?=$APPLICATION->GetCurPage()?>?mid=<?=$module_id?>&amp;lang=<?=$request['lang']?>' name='<?=$module_id?>_settings'>
        <?$tabControl->Begin();?>
        <?foreach ($aTabs as $aTab):?>
            <?if ($aTab['OPTIONS']):?>
                <?$tabControl->BeginNextTab();?>
                <?__AdmSettingsDrawList($module_id, $aTab['OPTIONS']);?>
            <?endif;?>
        <?endforeach;?>
        <?$tabControl->BeginNextTab();?>
        <?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
        <?=bitrix_sessid_post();?>
        <?$tabControl->Buttons(array('btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false, "btnSave" => true ));?>
        <?$tabControl->End();?>
        <?//need for tab_rights. If in $_REQUEST hasn't Update -> rights do not save?>
        <input type="hidden" name="Update" value="Y" />
    </form>
<?
if($request->isPost())
    LocalRedirect($APPLICATION->GetCurPage().'?lang='.LANGUAGE_ID.'&mid='.$module_id.
        '&tabControl_active_tab='.urlencode($_REQUEST['tabControl_active_tab']));
?>