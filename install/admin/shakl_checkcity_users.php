<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->SetTitle("Список пользователей");

$sTableID = "tbl_users";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$rsData = CUser::GetList(($by="id"), ($order="desc"));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint("Пользователи"));

// Экспорт в Excel
if($_REQUEST["export_excel"]=="Y")
{
    $lAdmin->ExportExcel();
}

$aHeaders = array(
    array("id"=>"ID", "content"=>'ID', "sort"=>"id", "default"=>true),
    array("id"=>"LOGIN", "content"=>'Login', "sort"=>"login", "default"=>true),
    array("id"=>"NAME", "content"=>'Name', "sort"=>"name", "default"=>true),
    array("id"=>"UF_REGCITY", "content"=>'City', "sort"=>"city", "default"=>true),
    array("id"=>"UF_REGREGION", "content"=>'Region', "sort"=>"region", "default"=>true),
);
$lAdmin->AddHeaders($aHeaders);

while($arRes = $rsData->NavNext(true, "f_")) {
    $row =& $lAdmin->AddRow($f_ID, $arRes);
    $row->AddViewField("ID", $f_ID);
    $row->AddViewField("LOGIN", $f_LOGIN);
    $row->AddViewField("NAME", $f_NAME);
    $row->AddViewField("UF_REGCITY", $f_REGCITY);
    $row->AddViewField("UF_REGREGION", $f_REGREGION);
}

$lAdmin->AddFooter(
    array(
        array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
        array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
    )
);

$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
