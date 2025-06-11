<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
global $USER;
?>

<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID?>">
<head>
    <? $APPLICATION->ShowHead();?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><? $APPLICATION->ShowTitle()?></title>
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH?>/css/style.css" />
    <?php if ($APPLICATION->GetCurDir() === '/'): ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/style-eventlist.css" />
    <?php endif; ?>
    <?php if ($APPLICATION->GetCurDir() === '/personal/'): ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/user.css" />
    <?php endif; ?>
    <?php if ($APPLICATION->GetCurDir() === '/personal/rating/'): ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/rating.css" />
    <?php endif; ?>

    <?php if ($APPLICATION->GetCurDir() === '/personal/attended-events/'): ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/attended-events.css" />
    <?php endif; ?>

    <?php if ($APPLICATION->GetCurDir() === '/personal/edit-event/'): ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/assign-points.css" />
    <?php endif; ?>

    <?php if ($APPLICATION->GetCurDir() === '/personal/add-event/'): ?>
        <!-- Flatpickr Date Picker CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/event-creation.css" />
    <?php endif; ?>

    <link rel="icon" href="<?= SITE_TEMPLATE_PATH?>/img/icon/favicon.ico" type="image/x-icon" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollToPlugin.min.js"></script>
    <script src=" https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js "></script>
    <link
            href="https://fonts.googleapis.com/css2?family=Jura:wght@400&display=swap"
            rel="stylesheet"
    />
</head>
<body>
<?//$APPLICATION->ShowPanel()?>
<header>
    <div class="header-container">
        <div class="header-top">
            <div class="logo-section">
                <div class="header-logo">
                    <a style="
    color: black;
    text-decoration: none;
" href="/" class="logotext">hse.движ</a>
                </div>
                <div class="header-divider"></div>
            </div>
            <div class="login-section">
                <?php if ($USER->IsAuthorized()) {
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
                    <a href="/personal/" class="login-btn">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/img/user.svg" class="user-lk-icon">
                    </a>
                    <style>
                        @media (max-width: 991px) {
                            .user-lk-icon{
                                width: 16px;
                            }
                        }
                    </style>
                <?php } else { ?>
                    <a href="/auth/" class="login-btn">Войти</a>
                <?php }?>

            </div>
        </div>
        <div class="header-links">
            <a href="/#afisha" class="header-link">Афиша</a>
            <a href="/#calendar-section" class="header-link">Календарь</a>
            <a href="/#achievements" class="header-link">Достижения</a>
        </div>

    </div>
</header>
