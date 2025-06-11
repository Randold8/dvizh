<?
global $USER;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("HSE Движ");
?>



<section class="events-section">
    <div class="events-container">
        <a href="#" class="event event-main">
            <img src="/img/event1.png" alt="Main Event" />
        </a>

        <div class="event-secondary-container">
            <a href="#" class="event event-secondary">
                <img src="/img/event2.png" alt="Secondary Event" />
            </a>
            <a href="#" class="arrow-link right-arrow">
                <span class="arrow">→</span>
            </a>
        </div>

        <div class="event-secondary-container reverse">
            <a href="#" class="event event-secondary">
                <img src="/img/event3.png" alt="Secondary Event" />
            </a>
            <a href="#" class="arrow-link left-arrow">
                <span class="arrow">←</span>
            </a>
        </div>

        <div class="date-display">
            <div class="date-container">
                <div id="day-number" class="day-number"></div>
                <div class="date-divider"><hr /></div>
                <div id="month-number" class="month-number"></div>
            </div>
            <a href="" id="afishaBtn2" class="events-link">все события</a>
        </div>

        <div class="shadow-divider"></div>
    </div>
</section>

<section class="list-section" id="afisha">
    <div class="list-container">
        <h2 class="section-heading">Афиша</h2>

        <div class="filter-container">
            <div class="filter-scroll">
                <button class="filter-btn active" data-filter="all">Все события</button>
                <button class="filter-btn" data-filter="lecture">Лекции</button>
                <button class="filter-btn" data-filter="exhibition">Выставки</button>
                <button class="filter-btn" data-filter="conference">Конференции</button>
                <button class="filter-btn" data-filter="quiz">Квизы</button>
                <button class="filter-btn" data-filter="other">Другое</button>
            </div>
        </div>

        <div class="events-list-container">
            <div class="events-list">
                <!-- Events will be dynamically inserted here -->
            </div>
            <div class="events-navigation desktop-only">
                <button class="nav-arrow nav-prev">←</button>
                <button class="nav-arrow nav-next">→</button>
            </div>
        </div>
    </div>
</section>
<section id="calendar-section" class="calendar-section">
    <div class="list-container">
        <h2 class="section-heading">Календарь событий</h2>
        <img src="/img/calendarsticker.png" class="calendar-sticker"></img>
        <div id="calendar"></div>
        <?php if ($USER->IsAdmin()): ?>
            <a href="/personal/add-event/" style="text-decoration: none" class="create-event-btn">Создать своё мероприятие</a>
        <?php endif; ?>
            <img src="/img/calendarsticker.png" class="calendar-sticker2"></img>


    </div>
</section>
<?php
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;

global $USER;
$isAuth = $USER->IsAuthorized();

$stars = 0;
$rank = 0;
$total = 0;

if ($isAuth) {
    Loader::includeModule("highloadblock");

    $hlblock = HighloadBlockTable::getById(3)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();

    // Получаем все записи рейтинга
    $allRatings = $entityClass::getList([
        'select' => ['ID', 'UF_USER_ID', 'UF_STARS'],
        'order'  => ['UF_STARS' => 'DESC']
    ])->fetchAll();

    $total = count($allRatings);
    foreach ($allRatings as $i => $row) {
        if ((int)$row['UF_USER_ID'] === (int)$USER->GetID()) {
            $stars = (int)$row['UF_STARS'];
            $rank = $i + 1;
            break;
        }
    }
}
?>

<section class="achievements-section" id="achievements">
    <div class="achievements-container">
        <h2 class="section-heading special-heading">Достижения</h2>
        <div class="achievements-display">
            <img src="<?= SITE_TEMPLATE_PATH ?>/img/staricon.png" class="stars">
            <?php if ($isAuth): ?>
                <p class="achievements-text">
                    У вас <?= $stars ?> звёзд.<br><br>
                    Ваш рейтинг: <?= $rank ?> из <?= $total ?>.<br><br>
                    <u>Запишитесь на ближайшие<br>мероприятия чтобы получить<br>больше звёзд!</u>
                </p>
            <?php else: ?>
                <p class="achievements-text">
                    Чтобы видеть ваш рейтинг<br>и звёзды, <a href="/auth/">войдите в личный кабинет</a>.<br><br>
                    <u>Запишитесь на ближайшие<br>мероприятия чтобы получить<br>больше звёзд!</u>
                </p>
            <?php endif; ?>
            <img class="asticker" src="<?= SITE_TEMPLATE_PATH ?>/img/asticker.png">
        </div>
        <div class="asticker-container">
            <img class="asticker-mobile" src="<?= SITE_TEMPLATE_PATH ?>/img/asticker.png">
        </div>
    </div>
</section>


<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>

