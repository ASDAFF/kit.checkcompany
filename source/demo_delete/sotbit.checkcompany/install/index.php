<?php
IncludeModuleLangFile(__FILE__);

class sotbit_checkcompany extends CModule
{
    const MODULE_ID = 'sotbit.checkcompany';
    var $MODULE_ID = 'sotbit.checkcompany';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $_1952561657 = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = GetMessage('sotbit.checkcompany_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('sotbit.checkcompany_MODULE_DESC');
        $this->PARTNER_NAME = GetMessage('sotbit.checkcompany_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('sotbit.checkcompany_PARTNER_URI');
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;
        RegisterModule(self::MODULE_ID);
        $this->InstallFiles();
        $this->InstallDB();
    }

    function InstallFiles($_2060282491 = array())
    {
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/themes/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes/', true, true);
        return true;
    }

    function InstallDB($_2060282491 = array())
    {

        $_1858929349 = \Bitrix\Main\EventManager::getInstance();
        $_1858929349->registerEventHandler('main', 'OnEpilog', self::MODULE_ID, '\Sotbit\Checkcompany\Eventhandler', 'OnEpilogHandler');
        RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, '\Sotbit\Checkcompany\Eventhandler', 'OnBuildGlobalMenuHandler');
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION;
        $this->UnInstallDB();
        UnRegisterModule(self::MODULE_ID);
        $this->UnInstallFiles();
        $GLOBALS['errors'] = $this->_98042048;
        $APPLICATION->IncludeAdminFile(GetMessage(self::MODULE_ID . '.MODULE_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/unstep2.php');
    }

    function UnInstallDB($_2060282491 = array())
    {
        $_1858929349 = \Bitrix\Main\EventManager::getInstance();
        $_1858929349->unRegisterEventHandler('main', 'OnEpilog', self::MODULE_ID, '\Sotbit\Checkcompany\Eventhandler', 'OnEpilogHandler');
        UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, '\Sotbit\Checkcompany\Eventhandler', 'OnBuildGlobalMenuHandler');
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/themes/.default/icons/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes/.default/icons');
        return true;
    }
}