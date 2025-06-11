<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<section class="event-detail-section">
    <div class="event-detail-container">

        <img
                id="event-image"
                class="detail_picture"
                border="0"
                src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
                width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
                height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
                alt="<?=htmlspecialchars($arResult["DETAIL_PICTURE"]["ALT"])?>"
                title="<?=htmlspecialchars($arResult["DETAIL_PICTURE"]["TITLE"])?>"
        />

        <div class="event-content-container">
            <div class="event-date-container">
                <div class="event-date">
                    <?=$arResult["PROPERTIES"]["F_DATE"]["VALUE"]?>
                    <?php if (!empty($arResult["PROPERTIES"]["F_TIME"]["VALUE"])): ?>
                        в <?=$arResult["PROPERTIES"]["F_TIME"]["VALUE"]?>
                    <?php endif; ?>
                </div>
                <div class="event-location">
                    <?=$arResult["PROPERTIES"]["F_PLACE"]["VALUE"] ?: 'Место уточняется'?>
                </div>
            </div>

            <div class="event-info">
                <h1><?=htmlspecialchars($arResult["NAME"])?></h1>
                <?php if (!empty($arResult["PREVIEW_TEXT"])): ?>
                    <p><strong><?=htmlspecialchars($arResult["PREVIEW_TEXT"])?></strong></p>
                <?php endif; ?>
                <p><?=nl2br(htmlspecialchars($arResult["DETAIL_TEXT"]))?></p>
            </div>
        </div>
    </div>

    <div class="event-action-buttons">
        <?php if ($USER->IsAuthorized()) {
            global $USER;
            $userId = $USER->GetID();
            $eventId = $arResult['ID'];
            $eventDate = $arResult["PROPERTIES"]["F_DATE"]["VALUE"];
            $eventStatus = $arResult["PROPERTIES"]["F_STATUS"]["VALUE"]; // "Y" если событие прошло

            // Если событие прошло — доступна только кнопка "Посетил(-а)"
            // Если не прошло — доступна только кнопка "Записаться"
            $attendedDisabled = ($eventStatus != 'Y') ? 'disabled' : '';
            $registerDisabled = ($eventStatus == 'Y') ? 'disabled' : '';

            $attendedStyle = ($attendedDisabled) ? 'background: gray;' : '';
            $registerStyle = ($registerDisabled) ? 'background: gray;' : '';
            ?>

            <button class="btn btn-attended"
                    data-user-id="<?= $userId ?>"
                    data-event-id="<?= $eventId ?>"
                    data-event-date="<?= $eventDate ?>"
                    data-status="ATTENDED"
                    style="<?= $attendedStyle ?>"
                <?= $attendedDisabled ?>>
                Посетил(-а)
            </button>

            <button class="btn btn-register"
                    data-user-id="<?= $userId ?>"
                    data-event-id="<?= $eventId ?>"
                    data-event-date="<?= $eventDate ?>"
                    data-status="REGISTERED"
                    style="<?= $registerStyle ?>"
                <?= $registerDisabled ?>>
                Записаться
            </button>
        <?php } ?>


    </div>
</section>
<div id="popup-message" style="
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #222;
    color: white;
    padding: 15px 30px;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.6);
    z-index: 9999;
    font-size: 16px;
    opacity: 0;
    transition: opacity 0.3s ease;
"></div>

<?php
// Подключаем jQuery из ядра Битрикса
CJSCore::Init(['jquery']);
?>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const popup = document.getElementById('popup-message');

        function showPopup(message) {
            popup.textContent = message;
            popup.style.display = 'block';
            // маленькая задержка для плавного появления
            setTimeout(() => popup.style.opacity = '1', 10);
            // через 2.5 секунды скрываем
            setTimeout(() => {
                popup.style.opacity = '0';
                // через 300мс после анимации скрываем display
                setTimeout(() => popup.style.display = 'none', 300);
            }, 2500);
        }

        document.querySelectorAll('.btn-attended, .btn-register').forEach(btn => {
            btn.addEventListener('click', () => {
                console.log(123);
                const userId = btn.dataset.userId;
                const eventId = btn.dataset.eventId;
                const status = btn.dataset.status;
                const eventDate = new Date(btn.dataset.eventDate);
                const today = new Date();
                today.setHours(0,0,0,0);

                let finalStatus = status;

                if (eventDate < today) {
                    finalStatus = 'ATTENDED';
                }

                fetch('/local/ajax/event_signup.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'BX-AJAX-POST': 'Y',
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        event_id: eventId,
                        status: finalStatus
                    }),
                    credentials: 'same-origin',
                })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            showPopup('Успешно!');
                            // можно тут добавить обновление интерфейса
                        } else {
                            showPopup('Ошибка: ' + (data.error || 'Неизвестная ошибка'));
                        }
                    })
                    .catch(() => showPopup('Ошибка при отправке запроса'));
            });
        });
    });
</script>


