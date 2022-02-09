<?php

CModule::AddAutoloadClasses('kit.checkcompany', array('CModuleOptions' => 'classes/general/CModuleOptions.php',));

class KitCheckompanyMain
{
    const MODULE_ID = 'kit.checkcompany';
    private static $_614285961 = false;

    public function __construct()
    {
    }

    public function returnDemo()
    {
        if (self::$_614285961 === false) self::setDemo();
        return static::$_614285961;
    }

    public static function setDemo()
    {
        if (self::$_614285961 === false) static::$_614285961 = CModule::IncludeModuleEx(self::MODULE_ID);
    }

    public function initSuggestions()
    {
        if (self::getDemo()) echo '$("#party").suggestions({
                    token: "' . \COption::GetOptionString(self::MODULE_ID, "API_KEY") . '",
                    type: "PARTY",
                    count: 10,
                    onSelect: showSuggestion
                });';
    }

    public function getDemo()
    {
        if (self::$_614285961 === false) self::setDemo();
        return !(static::$_614285961 == 0 || static::$_614285961 == 3);
    }
}