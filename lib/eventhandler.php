<?
namespace Kit\Checkcompany;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages ( __FILE__ );

class Eventhandler {

    public function OnBuildGlobalMenuHandler(&$arGlobalMenu, &$arModuleMenu){
        \Kit\Checkcompany\Helper\Menu::getAdminMenu($arGlobalMenu, $arModuleMenu);
    }

    public function onEpilogHandler()
    {
        $setLimit = \COption::GetOptionString("kit.checkcompany", 'EXECUTE_ON_SEPARATE_PAGES', "N");

        if ($setLimit == "Y") {
            global $APPLICATION;
            $url = $APPLICATION->GetCurPage(false);
            $listUrl = array_diff(unserialize(\COption::GetOptionString("kit.checkcompany", 'URL_TO_EXECUTE')), array(''));
            $isTemplate = self::checkTemplate($listUrl, $url);
        }

        if ($setLimit == "N" || $isTemplate) {
            \CModule::includeModule('sale');
            $persons = unserialize(\COption::GetOptionString("kit.checkcompany", 'PERSON_TYPES'));
            if (!is_array($persons)) {
                $persons = array();
            }
            $data = array();
            $inputs = array();
            global $APPLICATION;
            if (count($persons) > 0 && !strstr($APPLICATION->GetCurPage(), 'bitrix')) {
                foreach ($persons as $item) {
                    $inn = \COption::GetOptionString("kit.checkcompany", 'INN_' . $item);
                    $kpp = \COption::GetOptionString("kit.checkcompany", 'KPP_' . $item);
                    $ogrn = \COption::GetOptionString("kit.checkcompany", 'OGRN_' . $item);
                    $company = \COption::GetOptionString("kit.checkcompany", 'COMPANY_' . $item);
                    $address = \COption::GetOptionString("kit.checkcompany", 'ADDRESS_' . $item);
                    $f = \COption::GetOptionString("kit.checkcompany", 'F_' . $item);
                    $i = \COption::GetOptionString("kit.checkcompany", 'I_' . $item);
                    $o = \COption::GetOptionString("kit.checkcompany", 'O_' . $item);
                    $input = \COption::GetOptionString("kit.checkcompany", 'INPUT_' . $item);
                    $items = array();
                    $codes = array();
                    if ($inn != '-') {
                        $codes[] = $inn;
                    }
                    if ($kpp != '-') {
                        $codes[] = $kpp;
                    }
                    if ($ogrn != '-') {
                        $codes[] = $ogrn;
                    }
                    if ($company != '-') {
                        $codes[] = $company;
                    }
                    if ($address != '-') {
                        $codes[] = $address;
                    }
                    if ($f != '-') {
                        $codes[] = $f;
                    }
                    if ($i != '-') {
                        $codes[] = $i;
                    }
                    if ($o != '-') {
                        $codes[] = $o;
                    }
                    $db_props = \CSaleOrderProps::GetList(
                        array("SORT" => "ASC"),
                        array('CODE' => $codes, 'PERSON_TYPE_ID' => $item),
                        false,
                        false,
                        array('PERSON_TYPE_ID', 'ID', 'CODE')
                    );
                    while ($props = $db_props->fetch()) {
                        $items[$props['CODE']] = $props['ID'];
                    }
                    $inputs[$item] = $items[$input];
                    $data[$item]['inn'] = array('ID' => $items[$inn], 'CODE' => $inn);
                    $data[$item]['kpp'] = array('ID' => $items[$kpp], 'CODE' => $kpp);
                    $data[$item]['ogrn'] = array('ID' => $items[$ogrn], 'CODE' => $ogrn);
                    $data[$item]['company'] = array('ID' => $items[$company], 'CODE' => $company);
                    $data[$item]['address'] = array('ID' => $items[$address], 'CODE' => $address);
                    $data[$item]['f'] = array('ID' => $items[$f], 'CODE' => $f);
                    $data[$item]['i'] = array('ID' => $items[$i], 'CODE' => $i);
                    $data[$item]['o'] = array('ID' => $items[$o], 'CODE' => $o);
                    $data[$item]['main'] = array('ID' => $items[$input], 'CODE' => $input);
                }
                echo '<script type="text/javascript">
			function handler(e){
				var Request = new XMLHttpRequest();
				var params = \'query=\'+e.target.value;
				Request.open("GET", \'/bitrix/admin/checkcompany_ajax.php?\' + params, true);
				Request.onreadystatechange = function() {
					if (this.readyState != 4) return;
					if(this.responseText === "") return;
					var data = JSON.parse(this.responseText);
					if(data){
						var ul = document.createElement("ul");
						ul.style.margin = "0px";
						ul.style.width = e.target.style.width;
						ul.style.position = "absolute";
						ul.style.zIndex = "9999";
						ul.style.background = "#fff";
						var list = document.getElementById("list");
						if(list){
							list.remove();
						}
						ul.setAttribute("id", "list");
						var style = document.createElement(\'style\');
						style.type = \'text/css\';
						var h = \'#list li:hover p {color:#f00}\';
						var hover = document.createTextNode(h);
						var head = document.getElementsByTagName(\'head\')[0];
						style.appendChild(hover);
						head.appendChild(style);
						data.forEach(function(value, index){
							var item = document.createElement("li");
							item.style.listStyle = "none";
							item.style.padding = "5px 10px";
							item.style.zIndex = "9999";
							item.setAttribute("data-index", index);
							if(value.inn === e.target.value){
								setInfo(value);
								return;
							}
							item.onclick = function(e){
								var list = document.getElementById("list");
								if(list){
									list.remove();
								}
								setInfo(data[e.target.getAttribute("data-index")]);
							}
							var p = document.createElement("p");
							p.style.margin = "0px";
							p.innerHTML = value.company_name;
							var p2 = document.createElement("p");
							p2.innerHTML = value.inn+" "+value.address;
							p.setAttribute("data-index", index);
							p2.setAttribute("data-index", index);
							item.append(p);
							item.append(p2);
							
							ul.append(item);
						});
						e.target.after(ul);
					}
				}
			Request.send();
			}
			function setInfo(info)
			{
				var inn_id = ' . json_encode($inputs) . ';
				var fields = ' . json_encode($data) . ';
				var data_fields ="";
				var pType = "";
				
				
				for (var personType in inn_id) 
				{
					var element =  document.querySelector(\'input[name = ORDER_PROP_\'+inn_id[personType]+\']\');
					if(!element)
					{

						var ratio = document.getElementsByClassName("REGISTER_WHOLESALER_TYPE");
						for (i = 0; i < ratio.length; ++i)
						{
							if(ratio[i].checked && ratio[i].value == personType)
							{
								var element =
						 document.querySelector(\'input[name = "REGISTER_WHOLESALER_OPT[\'+personType+\'][\'+fields[personType]["main"]["CODE"]+\']"]\');
							}
						}
					}

					if(element){
						data_fields = fields[personType];
						pType = personType;
					}
				}

				var company = document.querySelector(\'input[name=ORDER_PROP_\'+data_fields.company.ID+\']\');
				if(!company)
				{
					var company = document.querySelector(\'input[name="REGISTER_WHOLESALER_OPT[\'+pType+\'][\'+data_fields.company.CODE+\']"]\');
				}
				
				var inn = document.querySelector(\'input[name=ORDER_PROP_\'+data_fields.inn.ID+\']\');
				if(!inn)
				{
					var inn = document.querySelector(\'input[name="REGISTER_WHOLESALER_OPT[\'+pType+\'][\'+data_fields.inn.CODE+\']"]\');
				}
				
				var kpp = document.querySelector(\'input[name=ORDER_PROP_\'+data_fields.kpp.ID+\']\');
				if(!kpp)
				{
					var kpp = document.querySelector(\'input[name="REGISTER_WHOLESALER_OPT[\'+pType+\'][\'+data_fields.kpp.CODE+\']"]\');
				}
				
				var ogrn = document.querySelector(\'input[name=ORDER_PROP_\'+data_fields.ogrn.ID+\']\');
				if(!ogrn)
				{
					var ogrn = document.querySelector(\'input[name="REGISTER_WHOLESALER_OPT[\'+pType+\'][\'+data_fields.ogrn.CODE+\']"]\');
				}
				
				var address = document.querySelector(\'input[name=ORDER_PROP_\'+data_fields.address.ID+\']\');
				if(!address){
						 address = document.querySelector(\'textarea[name=ORDER_PROP_\'+data_fields.address.ID+\']\');
				}
				if(!address)
				{
					var address = document.querySelector(\'input[name="REGISTER_WHOLESALER_OPT[\'+pType+\'][\'+data_fields.address.CODE+\']"]\');
				}
				
				var f = document.querySelector(\'input[name=ORDER_PROP_\'+data_fields.f.ID+\']\');
				if(!f)
				{
					var f = document.querySelector(\'input[name="REGISTER_WHOLESALER_OPT[\'+pType+\'][\'+data_fields.f.CODE+\']"]\');
				}
				
				var i = document.querySelector(\'input[name=ORDER_PROP_\'+data_fields.i.ID+\']\');
				if(!i)
				{
					var i = document.querySelector(\'input[name="REGISTER_WHOLESALER_OPT[\'+pType+\'][\'+data_fields.i.CODE+\']"]\');
				}
				
				var o = document.querySelector(\'input[name=ORDER_PROP_\'+data_fields.o.ID+\']\');
				if(!o)
				{
					var o = document.querySelector(\'input[name="REGISTER_WHOLESALER_OPT[\'+pType+\'][\'+data_fields.o.CODE+\']"]\');
				}

				if(info.company_name && company)  company.value = info.company_name;
				if(info.inn && inn)inn.value = info.inn;
				if(info.kpp && kpp) kpp.value = info.kpp;
				if(info.ogrn && ogrn)  ogrn.value = info.ogrn;
				if(info.address && address)  address.value = info.address;
				if(info.name !== null){
					var fio = info.name.split(" ");
					if(fio[0] && f)  f.value = fio[0];
					if(fio[1] && i)  i.value = fio[1];
					if(fio[2] && o) o.value = fio[2];
				}
			}
			function hideList(){
				setTimeout(function(){
					var list = document.getElementById("list");
						if(list){
							list.remove();
						}
				}, 1000)
			}
			function readyPage(){
				var inn_id = ' . json_encode($inputs) . ';
				var fields = ' . json_encode($data) . ';
				for (var personType in inn_id) 
				{
					var element =  document.querySelector(\'input[name=ORDER_PROP_\'+inn_id[personType]+\']\');
					if(!element)
					{
						var element = 
						 document.querySelector(\'input[name="REGISTER_WHOLESALER_OPT[\'+personType+\'][\'+fields[personType]["main"]["CODE"]+\']"]\');
					}
					if(element){
						element.oninput = handler;
						element.addEventListener("blur", hideList);
					}
				}
			}
			BX.addCustomEvent(\'onAjaxSuccess\', readyPage);
			document.addEventListener("DOMContentLoaded", readyPage);
			</script>';
            }
        }
    }

    static function checkTemplate($template, $url)
    {
        foreach ($template as $value){
            $value = str_replace(array(
                '*',
                '/'
            ), array(
                '.*',
                "\/"
            ), $value);

            if($value == "\/")
                $value = "^\/$";
            $result = preg_match('/'.$value.'/ui', $url);
            if($result){
                return $result;
            }
        }
    }
}