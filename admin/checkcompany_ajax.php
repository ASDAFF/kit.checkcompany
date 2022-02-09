<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

$module_id = "kit.checkcompany";
CModule::IncludeModule($module_id);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");


$module_status = CModule::IncludeModuleEx($module_id);
if($module_status == '0') {
    echo GetMessage('DEMO_MODULE');
}
elseif($module_status == '3'){
    echo GetMessage('DEMO_MODULE');
}

if(isset($_REQUEST['query']) && $_REQUEST['query'] != ''){
    $dadata = new \Kit\Checkcompany\Dadata();
    if (LANG_CHARSET != 'UTF-8'){
        $request = iconv(LANG_CHARSET, 'UTF-8', $_REQUEST['query']);
    } else {
        $request = $_REQUEST['query'];
    }
    echo json_encode($dadata->send($_REQUEST['query']));
}
die;


?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>