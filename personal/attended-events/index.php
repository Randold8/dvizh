<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");?>

<div class="personal-wrapper">
    <?global $USER;
    if ($USER->IsAuthorized()):
        $userId = $USER->GetID();
        $rsUser = CUser::GetByID($userId);
        $arUser = $rsUser->Fetch();
        $events = getVisitedEvents($userId);
        ?>
        <section class="attended-events-section">
            <div class="attended-events-container">
                <h2 class="page-heading">Посещенные мероприятия</h2>

                <?php if (!empty($events)): ?>
                    <div class="events-list">
                        <?php foreach ($events as $event): ?>
                            <a href="<?= htmlspecialchars($event['LINK']) ?>" class="event-row">
                                <?= htmlspecialchars($event['NAME']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Вы пока не посетили ни одного мероприятия.</p>
                <?php endif; ?>
            </div>
        </section>
    <?else:?>
        <p>Вам необходимо <a href="/auth/">авторизоваться</a> для доступа в личный кабинет.</p>
    <?endif;?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
