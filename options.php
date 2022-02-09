<?
use Bitrix\Main\Localization\Loc;

$module_id = "kit.checkcompany";
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
CModule::IncludeModule($module_id);

$arTabs = array();
IncludeModuleLangFile(__FILE__);


$APPLICATION->SetTitle(GetMessage($module_id.'_SETTING_TITLE'));

//пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅ
$CONS_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($CONS_RIGHT <= "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");

function OptionGetValue($key) {
    $result = COption::GetOptionString("kit.checkcompany",$key);
    if($_REQUEST[$key]){
        $result = $_REQUEST[$key];
    }
    return $result;
}

/**
 * custom parameter for the page addresses on which to connect the script
 */

$UrlPropsAdd = '<div id="UrlToExecute">';

if (!empty($_REQUEST['URL_TO_EXECUTE'])) {
    foreach ($_REQUEST['URL_TO_EXECUTE'] as $value) {
        $CurrentUrlPropsAdd[] = $value;
    }
} else {
    $CurrentUrlPropsAdd = unserialize(\Bitrix\Main\Config\Option::Get($module_id, "URL_TO_EXECUTE","",$_GET['site']));
}

if (!empty($_REQUEST['EXECUTE_ON_SEPARATE_PAGES']) && $_REQUEST['EXECUTE_ON_SEPARATE_PAGES']=="Y") {
    $availableUrl = "Y";
} else {
    $availableUrl = unserialize(\Bitrix\Main\Config\Option::Get($module_id, "EXECUTE_ON_SEPARATE_PAGES","N",$_GET['site']));
}

if($availableUrl=="Y"){
    $inputButtons = '<input type="button" value="+" onclick="new_row_status_add()" >
        <input type="button" value="-" onclick="delete_row_status_add()" >';
    $atrReadonly = '';
}
else{
    $inputButtons = '';
    $atrReadonly = 'readonly';
}

if (count($CurrentUrlPropsAdd) > 0 && is_array($CurrentUrlPropsAdd)) {
    foreach ($CurrentUrlPropsAdd as $value) {

        $UrlPropsAdd .= '<div>';
        $UrlPropsAdd .= '<input type="text" size="40"  value="'.$value.'" name="URL_TO_EXECUTE[]" '.  $atrReadonly .'/>';
        $UrlPropsAdd .= '</div>';
    }
} else {
    $UrlPropsAdd .= '<div>';
    $UrlPropsAdd .= '<input type="text" size="40"  value="'.$CurrentUrlPropsAdd.'" name="URL_TO_EXECUTE[]"  ' .  $atrReadonly . ' />';
    $UrlPropsAdd .= '</div>';
}

$UrlPropsAdd .= '
</div> '. $inputButtons .'


<script type="text/javascript">
    function new_row_status_add()
    {
        var div = document.createElement("div");
        div.innerHTML = \'';
$UrlPropsAdd .= '<input type="text" size="40"   name="URL_TO_EXECUTE[]" />';
$UrlPropsAdd .= '\';
        document.getElementById("UrlToExecute").appendChild(div);
    }
    function delete_row_status_add()
    {
        var ElCnt=document.getElementById("UrlToExecute").getElementsByTagName("div").length;
        if(ElCnt>1)
        {
            var children = document.getElementById("UrlToExecute").childNodes;
            document.getElementById("UrlToExecute").removeChild(children[children.length-1]);
        }
    }
</script>';

if( KitCheckompanyMain::returnDemo() == 2){
    ?>
    <div class="adm-info-message-wrap adm-info-message-red">
        <div class="adm-info-message">
            <div class="adm-info-message-title"><?=Loc::getMessage("CHECKCOMPANY_DEMO")?></div>
            <div class="adm-info-message-icon"></div>
        </div>
    </div>
    <?
}

if( KitCheckompanyMain::returnDemo() == 3 || KitCheckompanyMain::returnDemo() == 0)
{
    ?>
    <div class="adm-info-message-wrap adm-info-message-red">
        <div class="adm-info-message">
            <div class="adm-info-message-title"><?=Loc::getMessage("CHECKCOMPANY_DEMO_END")?></div>
            <div class="adm-info-message-icon"></div>
        </div>
    </div>
    <?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
    return '';
}

$arTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => GetMessage($module_id.'_edit1'),
        'ICON' => '',
        'TITLE' => GetMessage($module_id.'_edit1'),
        'SORT' => '10'
    )
);

$arGroups = array(
    'OPTION_5' => array('TITLE' => GetMessage($module_id.'_OPTION_5'), 'TAB' => 0),
);

$arOptions['API'] = array(
    'GROUP' => 'OPTION_5',
    'TITLE' =>  GetMessage($module_id.'_API_TITLE'),
    'TYPE' => 'SELECT',
    'VALUES'=> array('REFERENCE_ID'=>array('dadata'), 'REFERENCE'=>array('dadata.ru')),
    'SORT' => '50',
    'NOTES'=> GetMessage($module_id.'_API_DESC')
);
$arOptions['API_KEY'] = array(
    'GROUP' => 'OPTION_5',
    'TITLE' =>  GetMessage($module_id.'_API_KEY_TITLE'),
    'TYPE' => 'STRING',
    'SORT' => '60',
    'NOTES'=> GetMessage($module_id.'_API_KEY_DESC')
);
$arOptions['SECRET_KEY'] = array(
    'GROUP' => 'OPTION_5',
    'TITLE' =>  GetMessage($module_id.'_SECRET_KEY_TITLE'),
    'TYPE' => 'STRING',
    'SORT' => '60',
    'NOTES'=> GetMessage($module_id.'_SECRET_KEY_DESC')
);

$rs = Bitrix\Sale\Internals\PersonTypeTable::getList( array(
    'filter' => array(
        'ACTIVE' => 'Y',
    ),
    'select' => array(
        'ID',
        'NAME',
    )
) );
$person = array();
while ( $personType = $rs->fetch() )
{
    $data_person['REFERENCE'][] = $personType['NAME'];
    $data_person['REFERENCE_ID'][] = $personType['ID'];
    $person[$personType['ID']] = $personType['NAME'];
}
$arOptions['PERSON_TYPES'] = array(
    'GROUP' => 'OPTION_5',
    'TITLE' =>  GetMessage($module_id.'_COMPANY_TITLE'),
    'TYPE' => 'MSELECT',
    'VALUES'=>$data_person,
    'SORT' => '100',
    'NOTES'=> ''
);
$arOptions['EXECUTE_ON_SEPARATE_PAGES'] = array(
    'GROUP' => 'OPTION_5',
    'TITLE' =>  GetMessage($module_id.'_EXECUTE_ON_SEPARATE_PAGES'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y',
    'DEFAULT' => 'N',
    'NOTES'=> GetMessage($module_id.'_EXECUTE_ON_SEPARATE_PAGES_NOTES'),
    'SORT' => '150',
);
$arOptions['URL_TO_EXECUTE'] = array(
    'GROUP' => 'OPTION_5',
    'TITLE' =>  GetMessage($module_id.'_URL_TO_EXECUTE'),
    'TYPE' => 'CUSTOM',
    'VALUE' => $UrlPropsAdd,
    'NOTES'=> GetMessage($module_id.'_URL_TO_EXECUTE_NOTES'),
    'SORT' => '160',
);
//$types = unserialize(COption::GetOptionString("kit.checkcompany",'PERSON_TYPES'));
$types = OptionGetValue('PERSON_TYPES');
if(!is_array($types)) $types = unserialize($types);
if(count($types) > 0){
    $db_props = CSaleOrderProps::GetList(
        array("SORT" => "ASC"),
        array('PERSON_TYPE_ID'=>$types),
        false,
        false,
        array('PERSON_TYPE_ID', 'ID', 'NAME', 'CODE')
    );
    $props = array();
    while ($data = $db_props->fetch()){
        $props[$data['PERSON_TYPE_ID']][] = $data;
    }
    foreach ($props as $key => $prop) {
        $check = false;
        foreach ($prop as $item) {
            if($item['CODE'] == 'INN'){
                $check = true;
            }
        }
        if(!$check) unset($props[$key]);
    }

    foreach ($props as $key => $prop){
        $arGroups['PERSON_TYPE_'.$key] = array('TITLE' => $person[$key], 'TAB' => 0);
        $data_prop = array();
        $data_prop['REFERENCE'][] = '-';
        $data_prop['REFERENCE_ID'][] = '-';
        foreach ($prop as $item) {
            $data_prop['REFERENCE'][] = $item['NAME'];
            $data_prop['REFERENCE_ID'][] = $item['CODE'];
        }
        $arOptions['INPUT_'.$key] = array(
            'GROUP' => 'PERSON_TYPE_'.$key,
            'TITLE' =>  GetMessage($module_id.'_INPUT_TITLE'),
            'TYPE' => 'SELECT',
            'VALUES'=>$data_prop,
            'SORT' => '5',
            'NOTES'=> ''
        );
        $arOptions['COMPANY_'.$key] = array(
            'GROUP' => 'PERSON_TYPE_'.$key,
            'TITLE' =>  GetMessage($module_id.'_COMPANY_TITLE'),
            'TYPE' => 'SELECT',
            'VALUES'=>$data_prop,
            'SORT' => '10',
            'NOTES'=> ''
        );
        $arOptions['INN_'.$key] = array(
            'GROUP' => 'PERSON_TYPE_'.$key,
            'TITLE' =>  GetMessage($module_id.'_INN_TITLE'),
            'TYPE' => 'SELECT',
            'VALUES'=>$data_prop,
            'SORT' => '20',
            'NOTES'=> ''
        );
        $arOptions['KPP_'.$key] = array(
            'GROUP' => 'PERSON_TYPE_'.$key,
            'TITLE' =>  GetMessage($module_id.'_KPP_TITLE'),
            'TYPE' => 'SELECT',
            'VALUES'=>$data_prop,
            'SORT' => '30',
            'NOTES'=> ''
        );
        $arOptions['OGRN_'.$key] = array(
            'GROUP' => 'PERSON_TYPE_'.$key,
            'TITLE' =>  GetMessage($module_id.'_OGRN_TITLE'),
            'TYPE' => 'SELECT',
            'VALUES'=>$data_prop,
            'SORT' => '40',
            'NOTES'=> ''
        );
        $arOptions['ADDRESS_'.$key] = array(
            'GROUP' => 'PERSON_TYPE_'.$key,
            'TITLE' =>  GetMessage($module_id.'_ADDRESS_TITLE'),
            'TYPE' => 'SELECT',
            'VALUES'=>$data_prop,
            'SORT' => '50',
            'NOTES'=> ''
        );
        $arOptions['F_'.$key] = array(
            'GROUP' => 'PERSON_TYPE_'.$key,
            'TITLE' =>  GetMessage($module_id.'_F_TITLE'),
            'TYPE' => 'SELECT',
            'VALUES'=>$data_prop,
            'SORT' => '70',
            'NOTES'=> ''
        );
        $arOptions['I_'.$key] = array(
            'GROUP' => 'PERSON_TYPE_'.$key,
            'TITLE' =>  GetMessage($module_id.'_I_TITLE'),
            'TYPE' => 'SELECT',
            'VALUES'=>$data_prop,
            'SORT' => '80',
            'NOTES'=> ''
        );
        $arOptions['O_'.$key] = array(
            'GROUP' => 'PERSON_TYPE_'.$key,
            'TITLE' =>  GetMessage($module_id.'_O_TITLE'),
            'TYPE' => 'SELECT',
            'VALUES'=>$data_prop,
            'SORT' => '90',
            'NOTES'=> ''
        );

    }
}



/*
����������� ������ CModuleOptions
$module_id - ID ������
$arTabs - ������ ������� � �����������
$arGroups - ������ ����� ����������
$arOptions - ���������� ��� ������, ���������� ���������
$showRightsTab - ���������� ���� �� ���������� ������� � ����������� ���� ������� � ������ ( true / false )
*/

/*
$opt = new CModuleOptions($module_id, $arTabs, $arGroups, $arOptions, $showRightsTab);
$opt->ShowHTML();
*/
?>
    <a name="form"></a>
<?
$RIGHT = $APPLICATION->GetGroupRight($module_id);
if($RIGHT != "D") {


    if($RIGHT >= "W") {
        $showRightsTab = true;
    }

    $opt = new CModuleOptions($module_id, $arTabs, $arGroups, $arOptions, $showRightsTab);
    $opt->ShowHTML();
}


$tabControl = new CAdminTabControl("tabControl", $arTabs);
CJSCore::Init(array("jquery"));
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>