<?php

if (!isset($GLOBALS["USER"]) || !is_object($GLOBALS["USER"]) || !$GLOBALS["USER"]->IsAdmin()) {
    return;
}

if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shakl.checkcity/icon.gif")) {
    $iconPath = "/bitrix/modules/shakl.checkcity/icon.gif";
} else {
    $iconPath = "/bitrix/images/shakl.checkcity/icon.gif";
}

$aMenu = [
    [
        "parent_menu" => "global_menu_shakl",
        "section" => "shakl.checkcity",
        "sort" => 300,
        "text" => "Shakl Check City",
        "title" => "Shakl Check City",
        "icon" => "shakl_checkcity_menu_icon",
        "page_icon" => "shakl_checkcity_page_icon",
        "items_id" => "menu_shakl.checkcity",
        "items" => [
            [
                "text" => "View users",
                "url" => "shakl_checkcity.php",
                "more_url" => ["shakl_checkcity.php"],
                "title" => "View users",
            ],
        ]
    ],
];

return $aMenu;