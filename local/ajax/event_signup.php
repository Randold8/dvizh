<?php
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

header('Content-Type: application/json');

global $USER;

if (!$USER->IsAuthorized()) {
    echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
    exit;
}

$request = Application::getInstance()->getContext()->getRequest();

if ($request->getRequestMethod() != 'POST') {
    echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['user_id'], $data['event_id'], $data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Неверные данные']);
    exit;
}

$userId = (int)$data['user_id'];
$eventId = (int)$data['event_id'];
$status = $data['status'];

// Проверка на совпадение ID пользователя с текущим авторизованным
if ($userId !== (int)$USER->GetID()) {
    echo json_encode(['success' => false, 'error' => 'Ошибка аутентификации']);
    exit;
}

// ID HL-блока UserEventSignup — замени на свой
$hlblockId = 1;

Loader::includeModule('highloadblock');

$hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
if (!$hlblock) {
    echo json_encode(['success' => false, 'error' => 'HL-блок не найден']);
    exit;
}

$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();

try {
    // Проверяем есть ли уже запись
    $existing = $entityClass::getList([
        'filter' => ['UF_USER_ID' => $userId, 'UF_EVENT_ID' => $eventId],
        'select' => ['ID'],
    ])->fetch();

    if ($existing) {
        $entityClass::update($existing['ID'], ['UF_STATUS' => $status]);
    } else {
        $entityClass::add([
            'UF_USER_ID' => $userId,
            'UF_EVENT_ID' => $eventId,
            'UF_STATUS' => $status,
        ]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
