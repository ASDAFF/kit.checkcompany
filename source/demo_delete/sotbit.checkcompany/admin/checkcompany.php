<?
use Bitrix\Main\Localization\Loc;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
$module_id = "sotbit.checkcompany";
CModule::IncludeModule($module_id);
IncludeModuleLangFile(__FILE__);

$APPLICATION->SetTitle(GetMessage('CHECKCOMPANY_SETTING_TITLE'));

//�������� ����
$CONS_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($CONS_RIGHT <= "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");
CJSCore::Init(array("jquery"));

if( SotbitCheckompanyMain::returnDemo() == 2){
    ?>
    <div class="adm-info-message-wrap adm-info-message-red">
        <div class="adm-info-message">
            <div class="adm-info-message-title"><?=Loc::getMessage("CHECKCOMPANY_DEMO")?></div>
            <div class="adm-info-message-icon"></div>
        </div>
    </div>
    <?
}

if( SotbitCheckompanyMain::returnDemo() == 3 || SotbitCheckompanyMain::returnDemo() == 0)
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

?>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/js/jquery.suggestions.min.js"></script>
    <link rel="stylesheet prefetch" href="https://cdn.jsdelivr.net/npm/suggestions-jquery@latest/dist/css/suggestions.min.css">
    <section class="container">
        <input id="party" name="party" type="text" placeholder="<?=GetMessage($module_id.'PLACEHOLDER')?>" />
    </section>

    <section id="result">
        <p id="type"></p>
    </section>
    <script type="text/javascript">

        function join(arr /*, separator */) {
            var separator = arguments.length > 1 ? arguments[1] : ", ";
            return arr.filter(function(n){return n}).join(separator);
        }

        function typeDescription(type) {
            var TYPES = {
                'INDIVIDUAL': '<?=GetMessage($module_id.'IP')?>',
                'LEGAL': '<?=GetMessage($module_id.'ORG')?>'
            }
            return TYPES[type];
        }

        function showSuggestion(suggestion) {
            var data = suggestion.data;
            if (!data)
                return;
            $("#result").html('<p id="type"></p>');
            if (data.name)
                $("#result").append('<div class="row"><label><?=GetMessage($module_id.'SMALL_NAME')?> </label> <label id="inn">'+join([data.opf && data.opf.short || "", data.name.short || data.name.full], " ")+'</label> </div>');
            if (data.name && data.name.full)
            $("#result").append('<div class="row"><label><?=GetMessage($module_id.'FULL_NAME')?> </label> <label id="inn">'+join([data.opf && data.opf.full || "", data.name.full], " ")+'</label> </div>');
            $("#result").append('<div class="row"><label><?=GetMessage($module_id.'INN')?> </label> <label id="inn">'+data.inn+'</label> </div>');
            $("#result").append('<div class="row"><label><?=GetMessage($module_id.'KPP')?> </label> <label id="inn">'+data.kpp+'</label> </div>');
            $("#result").append('<div class="row"><label><?=GetMessage($module_id.'OGRN')?> </label> <label id="inn">'+data.ogrn+'</label> </div>');
            if (data.address)
            $("#result").append('<div class="row"><label><?=GetMessage($module_id.'ADDR')?> </label> <label id="inn">'+data.address.value+'</label> </div>');
            $("#type").text(
                typeDescription(data.type) + " (" + data.type + ")"
            );
        }

        <?=SotbitCheckompanyMain::initSuggestions()?>
        </script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
