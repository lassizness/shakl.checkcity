<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Bitrix\Main\Entity;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class Shakl_Checkcity extends CModule
{
    const MODULE_ID = 'shakl.checkcity';
    var $MODULE_ID = 'shakl.checkcity';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = "ШАКЛ - setCity";
        $this->MODULE_DESCRIPTION = "Модуль для записи в профиль пользователя города на момент регистрации, и региона по номеру телефона.";
        $this->PARTNER_NAME = "SHAKL tech";
        $this->PARTNER_URI = "https://shakl.tech";

    }

    function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $connection = Application::getConnection();

        // Добавление пользовательских полей
        $userField = new CUserTypeEntity();
        $userField->Add([
            "ENTITY_ID" => "USER",
            "FIELD_NAME" => "UF_REGCITY",
            "USER_TYPE_ID" => "string",
            "XML_ID" => "UF_REGCITY",
            "SORT" => 100,
            "MULTIPLE" => "N",
            "MANDATORY" => "N",
            "SHOW_FILTER" => "I",
            "SHOW_IN_LIST" => "",
            "EDIT_IN_LIST" => "",
            "IS_SEARCHABLE" => "N",
        ]);

        $userField->Add([
            "ENTITY_ID" => "USER",
            "FIELD_NAME" => "UF_REGREGION",
            "USER_TYPE_ID" => "string",
            "XML_ID" => "UF_REGREGION",
            "SORT" => 100,
            "MULTIPLE" => "N",
            "MANDATORY" => "N",
            "SHOW_FILTER" => "I",
            "SHOW_IN_LIST" => "",
            "EDIT_IN_LIST" => "",
            "IS_SEARCHABLE" => "N",
        ]);
        $this->InstallEvents();
        $this->InstallFiles();
    }

    function DoUninstall()
    {
        // Удаление пользовательских полей
        $dbUserType = CUserTypeEntity::GetList([], ["ENTITY_ID" => "USER", "FIELD_NAME" => "UF_REGCITY"]);
        if ($arUserType = $dbUserType->Fetch()) {
            $obUserField = new CUserTypeEntity;
            $obUserField->Delete($arUserType["ID"]);
        }

        $dbUserType = CUserTypeEntity::GetList([], ["ENTITY_ID" => "USER", "FIELD_NAME" => "UF_REGREGION"]);
        if ($arUserType = $dbUserType->Fetch()) {
            $obUserField = new CUserTypeEntity;
            $obUserField->Delete($arUserType["ID"]);
        }
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);

    }

    function InstallEvents()
    {
        //Добавление в главное меню глобального меню решений
        EventManager::getInstance()->registerEventHandler("main", "OnBuildGlobalMenu", $this->MODULE_ID, "CShaklCheckCityHandlers", "OnBuildGlobalMenu");
        return true;
    }

    function UnInstallEvents()
    {
        //Удаление из главного меню глобального меню решений
        EventManager::getInstance()->unRegisterEventHandler("main", "OnBuildGlobalMenu", $this->MODULE_ID, "CShaklCheckCityHandlers", "OnBuildGlobalMenu");
        return true;
    }

    function InstallFiles($arParams = array())
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin",true, true);
           return true;
    }
    function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
        return true;
    }
}
