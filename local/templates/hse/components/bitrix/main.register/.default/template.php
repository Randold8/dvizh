<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2024 Bitrix
 */

use Bitrix\Main\Web\Json;

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if($arResult["SHOW_SMS_FIELD"] == true)
{
	CJSCore::Init('phone_auth');
}
?>

<section class="register-section">
    <div class="register-container">
        <h2 class="register-header">Регистрация</h2>

        <?if($USER->IsAuthorized()):?>

            <p style="text-align: center"><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

        <?else: ?>
            <?
            if (!empty($arResult["ERRORS"])):
                foreach ($arResult["ERRORS"] as $key => $error)
                    if (intval($key) == 0 && $key !== 0)
                        $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

                ShowError(implode("<br />", $arResult["ERRORS"]));

            elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):
                ?>
                <p><?echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT")?></p>
            <?endif?>
            <form class="register-form" method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
                <?
                if($arResult["BACKURL"] <> ''):
                    ?>
                    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                <?
                endif;
                ?>
                <input type="text" name="REGISTER[NAME]" class="input-field" maxlength="50" placeholder="Введите имя*" required>
                <input type="text" name="REGISTER[LAST_NAME]" maxlength="50" class="input-field" placeholder="Введите фамилию*" required>
                <input type="text" name="REGISTER[SECOND_NAME]" maxlength="50" class="input-field" placeholder="Введите отчество*" required>
                <input type="text" name="REGISTER[LOGIN]" maxlength="255" class="input-field" placeholder="Введите логин*" required>
                <input type="email" name="REGISTER[EMAIL]" maxlength="255" class="input-field" placeholder="Введите почту*" required>
                <input type="text" name="UF_STATUS_GROUP" class="input-field" placeholder="Введите вашу группу/должность*">
                <input type="password" name="REGISTER[PASSWORD]" class="input-field" placeholder="Придумайте пароль*" required>
                <input type="password" name="REGISTER[CONFIRM_PASSWORD]" class="input-field" placeholder="Подтверждение пароля*" required>

                <input type="submit" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER")?>"  class="register-btn"/>
<!--                <button type="submit" class="register-btn">Зарегистрироваться</button>-->
            </form>
        <?endif ?>
    </div>
</section>