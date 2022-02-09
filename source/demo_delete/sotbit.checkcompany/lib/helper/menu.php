<?php

namespace Sotbit\Checkcompany\Helper;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

Loc::loadMessages(__FILE__);

class Menu
{
    public static function getAdminMenu(
        &$arGlobalMenu,
        &$arModuleMenu
    ) {

        if (!isset($arGlobalMenu['global_menu_sotbit'])) {
            $arGlobalMenu['global_menu_sotbit'] = [
                'menu_id'   => 'sotbit',
                'text'      => Loc::getMessage(
                    'CHECKCOMPANY_GLOBAL_MENU'
                ),
                'title'     => Loc::getMessage(
                    'CHECKCOMPANY_GLOBAL_MENU'
                ),
                'sort'      => 1000,
                'items_id'  => 'global_menu_sotbit_items',
                "icon"      => "",
                "page_icon" => "",
            ];
        }

        global $APPLICATION;
        if ($APPLICATION->GetGroupRight(\SotbitCheckompanyMain::MODULE_ID) != "D") {
            $aMenu = array(
                "parent_menu" => 'global_menu_sotbit',
                "section" => 'sotbit.checkcompany',
                "sort" => 850,
                "text" => Loc::getMessage("MENU_CHECKCOMPANY_TEXT"),
                "title" => Loc::getMessage("MENU_CHECKCOMPANY_TEXT"),
                "url" => "/bitrix/admin/checkcompany.php?lang=" . LANGUAGE_ID,
                "icon" => "checkcompany_menu_icon",
                "page_icon" => "checkcompany_page_icon",
            );

            $arGlobalMenu['global_menu_sotbit']['items']['sotbit.checkcompany'] = $aMenu;
        }
    }
}

?>