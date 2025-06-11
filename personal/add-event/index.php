<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Создание мероприятия");

use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Application;

Loader::includeModule("iblock");

global $USER;
$iblockId = 5; // ID инфоблока мероприятий

// Получаем варианты списка для свойства F_TYPE (список)
$fTypePropertyId = null;
$fTypeVariants = [];
$props = CIBlockProperty::GetList([], ['IBLOCK_ID' => $iblockId, 'CODE' => 'F_TYPE']);
if ($prop = $props->Fetch()) {
    $fTypePropertyId = $prop['ID'];
    $enum = CIBlockPropertyEnum::GetList([], ['PROPERTY_ID' => $fTypePropertyId]);
    while ($enumVal = $enum->Fetch()) {
        $fTypeVariants[$enumVal['ID']] = $enumVal['VALUE'];
    }
}

// Обработка POST-запроса
$errors = [];
$success = false;

if ($USER->IsAuthorized() && $USER->IsAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $request = Application::getInstance()->getContext()->getRequest();
    $name = trim($request->getPost('name'));
    $dateRaw = trim($request->getPost('date'));
    $time = trim($request->getPost('time'));
    $place = trim($request->getPost('place'));
    $description = trim($request->getPost('description'));
    $typeId = (int)$request->getPost('type');

    // Валидация
    if ($name === '') $errors[] = "Введите название мероприятия";
    if ($dateRaw === '') $errors[] = "Введите дату мероприятия";
    if ($time === '') $errors[] = "Введите время мероприятия";
    if ($place === '') $errors[] = "Введите место проведения";
    if ($description === '') $errors[] = "Введите описание";
    if (!isset($fTypeVariants[$typeId])) $errors[] = "Выберите корректный тип мероприятия";

    // Обработка даты в формат 20.11
    // Формат flatpickr скорее yyyy-mm-dd, преобразуем
    $dateObj = \DateTime::createFromFormat('d.m.Y', $dateRaw);
    if (!$dateObj) {
        $errors[] = "Некорректный формат даты";
    } else {
        $dateFormatted = $dateObj->format('d.m');
    }


    // Загрузка файла картинки
    $fileArray = $_FILES['event_image'] ?? null;
    $fileId = 0;
    if (!$fileArray || $fileArray['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Загрузите изображение мероприятия";
    } else {
        $fileId = CFile::SaveFile($fileArray, "events");
        if (!$fileId) {
            $errors[] = "Ошибка загрузки изображения";
        }
    }

    if (empty($errors)) {
        // Создаём элемент инфоблока
        $el = new CIBlockElement;

        $fields = [
            "IBLOCK_ID" => $iblockId,
            "NAME" => $name,
            "ACTIVE" => "Y",
            "PREVIEW_TEXT" => $description,
            "PREVIEW_PICTURE" => $fileArray,
            "DETAIL_TEXT" => $description,
            "DETAIL_PICTURE" => $fileArray,
            "PROPERTY_VALUES" => [
                "F_DATE" => $dateFormatted,
                "F_TIME" => $time,
                "F_PLACE" => $place,
                "F_TYPE" => $typeId,
            ],
        ];

        $newId = $el->Add($fields);
        if ($newId) {
            $success = true;
        } else {
            $errors[] = "Ошибка создания мероприятия: ".$el->LAST_ERROR;
            // Можно удалить загруженный файл при ошибке, если надо
        }
    }
}

?>

    <div class="personal-wrapper">
        <?php if ($USER->IsAuthorized() && $USER->IsAdmin()): ?>
            <section class="creation-section">
                <div class="creation-container">
                    <h2 class="page-heading">Создание мероприятия</h2>

                    <?php if ($success): ?>
                        <p style="color: green;">Мероприятие успешно создано!</p>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <ul style="color: red;">
                            <?php foreach ($errors as $error): ?>
                                <li><?=htmlspecialchars($error)?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="creation-form" novalidate>
                        <input type="text" name="name" class="input-field" placeholder="Введите название*" required value="<?=htmlspecialchars($_POST['name'] ?? '')?>">
                        <input type="text" id="event-date-picker" name="date" class="input-field" placeholder="Выберите дату*" required value="<?=htmlspecialchars($_POST['date'] ?? '')?>">
                        <input type="text" name="time" class="input-field" placeholder="Время*" required value="<?=htmlspecialchars($_POST['time'] ?? '')?>">
                        <input type="text" name="place" class="input-field" placeholder="Введите место проведения*" required value="<?=htmlspecialchars($_POST['place'] ?? '')?>">
                        <textarea name="description" class="input-field description-box" placeholder="Введите описание*" rows="4" required><?=htmlspecialchars($_POST['description'] ?? '')?></textarea>

                        <div class="file-upload-container">
                            <label for="event-image-upload" class="upload-label">Загрузить изображение*</label>
                            <input type="file" id="event-image-upload" name="event_image" accept="image/*" required hidden>
                            <span id="file-name-display">Файл не выбран</span>
                        </div>

                        <select name="type" class="input-field" required>
                            <option value="">Выберите тип мероприятия</option>
                            <?php foreach ($fTypeVariants as $id => $val): ?>
                                <option value="<?= $id ?>" <?= (isset($_POST['type']) && $_POST['type'] == $id) ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" class="btn-create">Создать мероприятие</button>
                    </form>
                </div>
            </section>

            <!-- Flatpickr Date Picker JS -->
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <!-- Russian locale for Flatpickr -->
            <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>
            <script>
                flatpickr("#event-date-picker", {
                    locale: "ru",
                    dateFormat: "d.m.Y",
                    allowInput: true,
                });

                // Показ имени загруженного файла
                const inputFile = document.getElementById('event-image-upload');
                const fileNameDisplay = document.getElementById('file-name-display');
                inputFile.addEventListener('change', () => {
                    fileNameDisplay.textContent = inputFile.files.length > 0 ? inputFile.files[0].name : 'Файл не выбран';
                });
            </script>
        <?php else: ?>
            <p>Вам необходимо <a href="/auth/">авторизоваться</a> для доступа в личный кабинет.</p>
        <?php endif; ?>
    </div>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
