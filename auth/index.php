<?
// обратите внимание на эту константу
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация и регистрация");
global $USER;
if ($USER->IsAuthorized()) {
    LocalRedirect('/');
}
?>
    <p>Вы зарегистрированы и успешно авторизовались.</p>
<?
// ссылка для выхода из личного кабинета
$logout = $APPLICATION->GetCurPageParam(
    "logout=yes&" . bitrix_sessid_get(),
    array(
        "login",
        "logout",
        "register",
        "forgot_password",
        "change_password"
    )
);
?>
    <p><a href="<?= $logout; ?>">Выйти</a></p>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>