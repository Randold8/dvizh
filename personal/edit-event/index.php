<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактирование мероприятия");

use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;
use Bitrix\Main\Type\DateTime;

if (!Loader::includeModule("iblock")) {
    echo "Модуль инфоблоков не подключен";
    return;
}

Loader::includeModule('highloadblock');

global $USER;

$eventId = (int)$_GET['EVENT_ID'];

function getHLBlockEntity($hlblockId) {
    $hlblock = HighloadBlockTable::getById($hlblockId)->fetch();
    return HighloadBlockTable::compileEntity($hlblock);
}

$UserEventSignupEntity = getHLBlockEntity(1); // UserEventSignup
$UserRatingEntity = getHLBlockEntity(3);      // UserRating
$EventPointsLogEntity = getHLBlockEntity(2);  // EventPointsLog

$UserEventSignup = $UserEventSignupEntity->getDataClass();
$UserRating = $UserRatingEntity->getDataClass();
$EventPointsLog = $EventPointsLogEntity->getDataClass();

// Завершаем мероприятие
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'finish_event' && $USER->IsAdmin()) {
    CIBlockElement::SetPropertyValuesEx($eventId, 5, ['F_STATUS' => 10]);
    LocalRedirect($APPLICATION->GetCurPageParam('', ['action']));
}

// Начисление баллов
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['points']) && $USER->IsAdmin()) {
    foreach ($_POST['points'] as $userId => $points) {
        $userId = (int)$userId;
        $points = (int)$points;
        if ($points <= 0) continue;

        // Проверка, начислялись ли уже баллы за это мероприятие
        $existing = $EventPointsLog::getList([
            'filter' => ['=UF_EVENT_ID' => $eventId, '=UF_USER_ID' => $userId],
            'limit' => 1
        ])->fetch();

        if ($existing) {
            // Уже начислено — пропускаем
            continue;
        }

        // Получаем рейтинг пользователя
        $rating = $UserRating::getList([
            'filter' => ['=UF_USER_ID' => $userId],
            'limit' => 1
        ])->fetch();

        if ($rating) {
            // Обновляем звезды
            $newStars = $rating['UF_STARS'] + $points;
            $UserRating::update($rating['ID'], [
                'UF_STARS' => $newStars,
                'UF_UPDATED_AT' => new DateTime()
            ]);
        } else {
            // Создаем новую запись
            $UserRating::add([
                'UF_USER_ID' => $userId,
                'UF_STARS' => $points,
                'UF_UPDATED_AT' => new DateTime()
            ]);
        }

        // Записываем событие начисления в лог
        $EventPointsLog::add([
            'UF_EVENT_ID' => $eventId,
            'UF_USER_ID' => $userId,
            'UF_POINTS' => $points,
            'UF_DATE_ADDED' => new DateTime()
        ]);
    }
    // Перезагрузка страницы после начисления, чтобы обновить состояние
    LocalRedirect($APPLICATION->GetCurPageParam('', ['action']));
}

if ($USER->IsAuthorized() && $USER->IsAdmin() && $eventId > 0):
    // Получаем мероприятие
    $event = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => 5, 'ID' => $eventId],
        false,
        false,
        ['ID', 'NAME', 'IBLOCK_ID', 'PROPERTY_F_STATUS']
    )->Fetch();

    if (!$event) {
        echo "Мероприятие не найдено.";
        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
        return;
    }

    $isFinished = $event['PROPERTY_F_STATUS_VALUE'] === 'Y';

    // Получаем участников мероприятия (со статусом 'Y' - посетили)
    $eventVisitors = $UserEventSignup::getList([
        'filter' => ['=UF_EVENT_ID' => $eventId],
        'select' => ['ID', 'UF_USER_ID']
    ])->fetchAll();

    // Получаем пользователей, которым уже начислялись баллы
    $userIds = array_column($eventVisitors, 'UF_USER_ID');
    if (empty($userIds)) $userIds = [0]; // чтоб запрос не упал если пусто

    $pointsLogs = $EventPointsLog::getList([
        'filter' => ['=UF_EVENT_ID' => $eventId, '@UF_USER_ID' => $userIds],
        'select' => ['UF_USER_ID', 'UF_POINTS']
    ])->fetchAll();

    $usersWithPoints = [];
    foreach ($pointsLogs as $log) {
        $usersWithPoints[$log['UF_USER_ID']] = $log['UF_POINTS'];
    }
    ?>
    <div class="personal-wrapper">
        <section class="assignment-section">
            <div class="assignment-container">
                <h2 class="page-heading"><?= $isFinished ? 'Начисление баллов' : htmlspecialchars($event['NAME']) ?></h2>

                <?php if (!$isFinished): ?>
                    <!-- Кнопка завершения мероприятия -->
                    <form method="POST" style="text-align: center;">
                        <input type="hidden" name="action" value="finish_event">
                        <button style="max-width: 300px; margin: auto" type="submit" class="btn btn-submit btn-finish-event">Завершить мероприятие</button>
                    </form>
                <?php else: ?>
                    <!-- Интерфейс начисления баллов -->
                    <?php if (!empty($eventVisitors)): ?>
                        <form method="POST" style="max-width: 600px; margin: auto;">
                        <div class="assignment-list">
                            <div class="assignment-header-row">
                                <span class="col-student-name">ФИО студента</span>
                                <span class="col-student-group">Академ. группа</span>
                                <span class="col-points">Кол-во баллов</span>
                                <span class="col-action"></span>
                            </div>

                            <?php foreach ($eventVisitors as $visitor):
                                $userId = $visitor['UF_USER_ID'];
                                $user = CUser::GetByID($userId)->Fetch();
                                $fio = trim($user['LAST_NAME'].' '.$user['NAME'].' '.$user['SECOND_NAME']);
                                $group = $user['UF_STATUS_GROUP'];

                                $points = $usersWithPoints[$userId] ?? null;
                                $disabled = $points !== null ? 'disabled' : '';
                                ?>
                                <div class="assignment-row">
                                    <span class="student-name"><?=htmlspecialchars($fio)?></span>
                                    <span class="student-group"><?=htmlspecialchars($group)?></span>
                                    <input
                                            type="number"
                                            name="points[<?=$userId?>]"
                                            class="points-input"
                                            placeholder="0"
                                            min="0"
                                            value="<?=htmlspecialchars($points ?? '')?>"
                                        <?=$disabled?>
                                    >
                                    <button class="btn-submit" type="submit" <?=$disabled?>>Начислить баллы</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                    <?php else: ?>
                        <p style="text-align: center">Не было посещений</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
<?php else: ?>
    <p>Вам необходимо <a href="/auth/">авторизоваться</a> для доступа в личный кабинет.</p>
<?php
endif;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
