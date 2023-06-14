<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Shakl\Checkcity\PhoneNumberChecker;

IncludeModuleLangFile(__FILE__);

// проверка прав доступа
if($APPLICATION->GetGroupRight("main")<"R")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

// обработка нажатия кнопки
if($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid()) {
    // количество пользователей для обработки
    $count = intval($_POST['COUNT']);

    // Подключение нашего апи
    $checker = new PhoneNumberChecker("https://api.regius.name/iface/phone-number.php", "6cc4d547ab0d263c096c9e71beb0fa67a5f4de2d");

    // получение списка пользователей
    $rsUsers = CUser::GetList(($by="ID"), ($order="desc"), Array(), array("SELECT" => array("ID", "UF_BXMAKER_AUPHONE")));
    $i = 0;
    while($arUser = $rsUsers->Fetch()) {
        if (!empty($arUser["UF_REGREGION"])) {
            continue;
        }
        $user = new CUser;
        $Region=$checker->checkPhoneNumber($arUser["UF_BXMAKER_AUPHONE"]);
        $fields = array(
            "UF_REGREGION" => $Region["region"],
        );
        $user->Update($arUser['ID'], $fields);
        $i++;
        if ($i % 5 == 0) {
            sleep(1); // Пауза в 1 секунду каждую пятую итерацию
        }
        if($i >= $count)
            break;
    }

    CAdminMessage::ShowMessage(array("MESSAGE"=>'Обработка завершена', "TYPE"=>"OK"));
}

// форма
$aTabs = array(
    array("DIV" => "edit1", "TAB" => "Обновление пользователей", "ICON"=>"main_user_edit", "TITLE"=>"Обновление пользователей"),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<form method="POST" action="<?=$APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="post_form">
    <?=bitrix_sessid_post()?>
    <tr>
        <td width="40%">Количество пользователей:</td>
        <td width="60%">
            <input type="text" name="COUNT" value="" size="50">
        </td>
    </tr>
    <?$tabControl->Buttons();?>
    <input type="submit" name="Update" value="Начать обновление" title="Начать обновление" class="adm-btn-save">
</form>
<?$tabControl->End();?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
