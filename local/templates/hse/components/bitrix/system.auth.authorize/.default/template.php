<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<section class="login-section-form">
    <div class="login-container">
        <h2 class="login-header">Вход</h2>

        <?
        if (!empty($arParams["~AUTH_RESULT"]))
        {
            ShowMessage($arParams["~AUTH_RESULT"]);
        }

        if (!empty($arResult['ERROR_MESSAGE']))
        {
            ShowMessage($arResult['ERROR_MESSAGE']);
        }
        ?>
        <form class="login-form" name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

            <input type="hidden" name="AUTH_FORM" value="Y" />
            <input type="hidden" name="TYPE" value="AUTH" />
            <?if ($arResult["BACKURL"] <> ''):?>
                <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
            <?endif?>
            <?foreach ($arResult["POST"] as $key => $value):?>
                <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
            <?endforeach?>
            <input type="text" class="input-field" placeholder="Введите логин (почту)*" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" required>
            <input type="password" class="input-field" placeholder="Введите пароль*" name="USER_PASSWORD" maxlength="255" autocomplete="off" required>

            <!-- Updated buttons container -->
            <div class="login-buttons-container">
                <button type="submit" class="login-btn-form">Войти</button>

                <?if($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y"):?>
                <a href="<?=$arResult["AUTH_REGISTER_URL"]?>" class="register-link-btn">Зарегистрироваться</a>
                <?endif?>
            </div>
        </form>
    </div>
</section>

<script>
<?if ($arResult["LAST_LOGIN"] <> ''):?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?else:?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?endif?>
</script>
