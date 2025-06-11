<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");?>

<div class="personal-wrapper">
    <?php
    global $USER;
    if ($USER->IsAuthorized()):
        $userId = $USER->GetID();
        $rsUser = CUser::GetByID($userId);
        $arUser = $rsUser->Fetch();

        // Проверка на администратора
        $isAdmin = $USER->IsAdmin();

        // Общие поля
        $fullName = trim($arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME']);
        $email = $arUser['EMAIL'];
        $group = $arUser['UF_STATUS_GROUP'] ?: '—';

        if ($isAdmin): ?>
            <section class="profile-section">
                <div class="profile-container admin-container">
                    <div class="profile-main-content">
                        <div class="profile-image-container">
                            <img id="profile-image" src="<?= SITE_TEMPLATE_PATH ?>/img/студент.png">
                            <input type="file" id="upload-avatar" hidden accept="image/*">
                        </div>
                        <div class="profile-details-container">
                            <div class="profile-info-container">
                                <input type="text" class="profile-info-field half-width" value="<?= htmlspecialchars($fullName) ?>" readonly>
                                <input type="email" class="profile-info-field three-quarter-width" value="<?= htmlspecialchars($email) ?>" readonly>
                                <input type="text" class="profile-info-field full-width" value="<?= htmlspecialchars($group) ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="admin-events-section">
                        <h2 class="section-subheading">Мероприятия</h2>
                        <div class="admin-events-list">
                            <?php
                            // Получаем мероприятия из инфоблока ID 5
                            \Bitrix\Main\Loader::includeModule("iblock");
                            $res = CIBlockElement::GetList(
                                ['ACTIVE_FROM' => 'DESC'],
                                ['IBLOCK_ID' => 5, 'ACTIVE' => 'Y'],
                                false,
                                false,
                                ['ID', 'NAME', 'DETAIL_PAGE_URL']
                            );
                            while ($event = $res->GetNext()):
                                ?>
                                <a href="/personal/edit-event/?EVENT_ID=<?= $event['ID'] ?>" class="admin-event-row">
                                    <span class="event-name-field"><?= htmlspecialchars($event['NAME']) ?></span>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </section>

        <?php else:
            // Студенческий интерфейс
            $infoUser = getUserInfoRating($userId);
            ?>
            <section class="profile-section">
                <div class="profile-container">
                    <div class="profile-image-container">
                        <img id="profile-image" src="<?= SITE_TEMPLATE_PATH ?>/img/студент.png">
                        <input type="file" id="upload-avatar" hidden accept="image/*">
                    </div>
                    <div class="profile-details-container">
                        <div class="profile-info-container">
                            <input type="text" id="user-name" class="profile-info-field half-width" value="<?= htmlspecialchars($fullName) ?>" readonly>
                            <input type="email" id="user-email" class="profile-info-field three-quarter-width" value="<?= htmlspecialchars($email) ?>" readonly>
                            <input type="text" id="user-group" class="profile-info-field full-width" value="<?= htmlspecialchars($group) ?>" readonly>
                        </div>
                        <div class="user-achievements-summary">
                            Кол-во посещенных мероприятий: <?= getVisitedEventCount($userId); ?><br>
                            Баланс: <?= $infoUser["STARS"]; ?> коинов<br>
                            Рейтинг: <?= $infoUser["RANK"]; ?> из <?= $infoUser["TOTAL"]; ?>
                        </div>
                        <div class="profile-action-buttons">
                            <a href="/personal/attended-events" class="btn">Посещенные мероприятия</a>
                            <a href="/personal/rating" class="btn">Рейтинг</a>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php else: ?>
        <p>Вам необходимо <a href="/auth/">авторизоваться</a> для доступа в личный кабинет.</p>
    <?php endif; ?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
