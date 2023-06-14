<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

Loader::includeModule('main');

$module_id = "shakl.checkcity";
$arClassesList = array(
    "Shakl\\Checkcity\\PhoneNumberChecker" => "lib/PhoneNumberChecker.php",
);

use Shakl\Checkcity\PhoneNumberChecker;
Loader::registerAutoLoadClasses($module_id, $arClassesList);
class ShaklCheckcityHandlers
{
    public static function onAfterUserRegisterHandler(&$arFields)
    {

        // В $arFields["USER_ID"] - ID зарегистрированного пользователя
        $userID = $arFields["USER_ID"];
        $userPhone = $arFields["UF_BXMAKER_AUPHONE"];

        //Подключаем сторонний модуль отвечающие за ГЕО сообщения.
        $oManager = \BXmaker\GeoIP\Manager::getInstance();
        $City = $oManager->getCity();

        //Подключаем наш класс отвечающий за работу с api стороннего сервиса
        $checker = new PhoneNumberChecker("https://api.regius.name/iface/phone-number.php", "6cc4d547ab0d263c096c9e71beb0fa67a5f4de2d");
        $data = $checker->checkPhoneNumber($userPhone);

        if ($data) {
            $Region = $data["region"];
        } else {
            $Region = "";
        }
        // Заполнение пользовательских полей
        $user = new CUser;
        $fields = array(
            "UF_REGCITY" => $City,
            "UF_REGREGION" => $Region,
        );
        $user->Update($userID, $fields);
    }

    public static function onBeforeUserRegisterHandler(&$arFields)
    {
        // Ваш код обработчика перед регистрацией пользователя
       // CustomAddMessage2Log("событие прошло до создания юзера", "shakl.checkcity");
    }
}

$eventManager = EventManager::getInstance();

$eventManager->addEventHandler('main', 'OnAfterUserRegister', ['ShaklCheckcityHandlers', 'onAfterUserRegisterHandler']);
$eventManager->addEventHandler('main', 'OnBeforeUserRegister', ['ShaklCheckcityHandlers', 'onBeforeUserRegisterHandler']);

class CShaklCheckCityHandlers
{
    function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        $MODULE_ID = 'shakl.checkcity';

        // проверяем, что текущий пользователь имеет право управления модулем
        if (!Loader::includeModule($MODULE_ID) || !$GLOBALS['APPLICATION']->GetGroupRight($MODULE_ID))
            return;

        // добавляем раздел в главное меню
        if (!isset($aGlobalMenu['global_menu_shakl'])) {
            $aGlobalMenu['global_menu_shakl'] = array(
                'menu_id' => 'shakl',
                'text' => 'ШАКЛ',
                'title' => 'Меню модулей ШАКЛ',
                'sort' => 1000,
                'items_id' => 'global_menu_shakl',
                'items' => array()
            );
        }

        // добавляем ссылку на страницу настроек модуля в созданный раздел
        $aModuleMenu[] = array(
            'parent_menu' => 'global_menu_shakl',
            'icon' => '',
            'page_icon' => '',
            'sort' => '100',
            'text' => 'Модуль CheckCity',
            'title' => 'Модуль CheckCity',
            'url' => '/bitrix/admin/shakl_checkcity_settings.php',
            'items_id' => 'menu_shakl_checkcity',
            'items' => array(
                array(
                    'text' => 'Просмотреть список пользователей',
                    'url' => '/bitrix/admin/shakl_checkcity_users.php',
                    'title' => 'Просмотреть список пользователей',
                    'more_url' => array(),
                    'items_id' => 'menu_shakl_checkcity_users',
                ),
                array(
                    'text' => 'Заполнение поля Региона',
                    'url' => '/bitrix/admin/shakl_checkcity_setregionoldusers.php',
                    'title' => 'Перейти к заполнению формы региона',
                    'more_url' => array(),
                    'items_id' => 'menu_shakl_checkcity_setregionoldusers',
                ),
                array(
                    'text' => 'Форма обратной связи',
                    'url' => '/bitrix/admin/shakl_checkcity_feedback.php',
                    'title' => 'Перейти к форме обратной связи',
                    'more_url' => array(),
                    'items_id' => 'menu_shakl_checkcity_feedback',
                )
            )
        );
    }
}
