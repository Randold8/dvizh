<?php
use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\IO\File;
use Bitrix\Highloadblock\HighloadBlockTable;

AddEventHandler('iblock', 'OnAfterIBlockElementUpdate', 'updateEventsJson');
AddEventHandler('iblock', 'OnAfterIBlockElementAdd', 'updateEventsJson');

function updateEventsJson($arFields) {
    if ($arFields['IBLOCK_ID'] != 5 || $arFields['RESULT'] === false) {
        return;
    }

    if (!Loader::includeModule('iblock')) {
        return;
    }

    $elements = [];
    $res = CIBlockElement::GetList(
        ['SORT' => 'ASC'],
        ['IBLOCK_ID' => 5, 'ACTIVE' => 'Y'],
        false,
        false,
        ['ID', 'NAME', 'PREVIEW_PICTURE', 'PROPERTY_F_DATE', 'PROPERTY_F_TIME', 'PROPERTY_F_PLACE', 'PROPERTY_F_TYPE']
    );

    while ($ob = $res->GetNext()) {
        $typeXmlId = '';
        $propRes = CIBlockElement::GetProperty(5, $ob['ID'], [], ['CODE' => 'F_TYPE']);
        if ($prop = $propRes->Fetch()) {
            $typeXmlId = $prop['VALUE_XML_ID'];
        }

        $elements[] = [
            'id'       => (int)$ob['ID'],
            'title'    => $ob['NAME'],
            'date'     => $ob['PROPERTY_F_DATE_VALUE'],
            'image'    => CFile::GetPath($ob['PREVIEW_PICTURE']),
            'time'     => $ob['PROPERTY_F_TIME_VALUE'],
            'location' => $ob['PROPERTY_F_PLACE_VALUE'],
            'type'     => $typeXmlId,
        ];
    }


    $json = json_encode($elements, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $file = new File($_SERVER['DOCUMENT_ROOT'] . '/events.json');
    $file->putContents($json);
}

AddEventHandler("main", "OnAfterUserAdd", "addUserRatingEntry");

function addUserRatingEntry(&$arFields) {
    if (empty($arFields["ID"])) return;

    \Bitrix\Main\Loader::includeModule("highloadblock");

    $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById(3)->fetch();
    $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();

    // Добавляем запись
    $entityClass::add([
        'UF_USER_ID'    => $arFields["ID"],
        'UF_STARS'      => 0,
        'UF_UPDATED_AT' => new \Bitrix\Main\Type\DateTime()
    ]);
}


function getUserInfoRating($userId) {
    Loader::includeModule("highloadblock");

    $hlblock = HighloadBlockTable::getById(3)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();

    $allRatings = $entityClass::getList([
        'select' => ['ID', 'UF_USER_ID', 'UF_STARS'],
        'order'  => ['UF_STARS' => 'DESC']
    ])->fetchAll();

    $total = count($allRatings);
    $stars = 0;
    $rank = 0;
    foreach ($allRatings as $i => $row) {
        if ($row['UF_USER_ID'] == $userId) {
            $stars = (int)$row['UF_STARS'];
            $rank = $i + 1;
            break;
        }
    }
    return ["RANK" => $rank, "STARS" => $stars,  "TOTAL" => $total];
}

function getUserBalance($userId) {
    if (!Loader::includeModule('highloadblock')) return 0;

    $hlblock = HighloadBlockTable::getById(3)->fetch(); // UserRating
    if (!$hlblock) return 0;

    $entity = HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();

    $row = $entityClass::getList([
        'filter' => ['UF_USER_ID' => $userId],
        'select' => ['UF_STARS'],
        'limit' => 1
    ])->fetch();

    return (int)(isset($row['UF_STARS']) ? $row['UF_STARS'] : 0); // предполагаем, что поле называется UF_BALANCE
}

function getVisitedEventCount($userId) {
    $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList([
        'filter' => ['=NAME' => 'UserEventSignup']
    ])->fetch();

    if (!$hlblock) return 0;

    $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();

    return $entityClass::getCount(['UF_USER_ID' => $userId, 'UF_STATUS' => 'ATTENDED']);
}
function getVisitedEvents($userId) {
    \Bitrix\Main\Loader::includeModule("highloadblock");
    \Bitrix\Main\Loader::includeModule("iblock");

    // Шаг 1: Получаем из HL-блока посещённые мероприятия
    $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById(1)->fetch();
    if (!$hlblock) return [];

    $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();

    $hlRows = $entityClass::getList([
        'filter' => ['UF_USER_ID' => $userId, 'UF_STATUS' => 'ATTENDED'],
        'select' => ['UF_EVENT_ID']
    ])->fetchAll();

    $eventIds = array_column($hlRows, 'UF_EVENT_ID');
    if (empty($eventIds)) return [];

    // Шаг 2: Получаем данные по мероприятиям из инфоблока ID=5
    $result = [];
    $res = \CIBlockElement::GetList(
        ['ACTIVE_FROM' => 'DESC'],
        ['IBLOCK_ID' => 5, 'ID' => $eventIds, 'ACTIVE' => 'Y'],
        false,
        false,
        ['ID', 'NAME', 'DETAIL_PAGE_URL']
    );

    while ($event = $res->GetNext()) {
        $result[] = [
            'NAME' => $event['NAME'],
            'LINK' => $event['DETAIL_PAGE_URL'] ?: '#',
        ];
    }

    return $result;
}


