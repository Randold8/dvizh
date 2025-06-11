<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");?>

<div class="personal-wrapper">
    <?php
    use Bitrix\Main\Loader;
    use Bitrix\Highloadblock\HighloadBlockTable;
    global $USER;
    if ($USER->IsAuthorized()):
        Loader::includeModule("highloadblock");

        $hlblock = HighloadBlockTable::getById(3)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        $entityClass = $entity->getDataClass();

        $allRatings = $entityClass::getList([
            'select' => ['ID', 'UF_USER_ID', 'UF_STARS'],
            'order'  => ['UF_STARS' => 'DESC']
        ])->fetchAll();

        $userId = $USER->GetID();
        $ratingRows = [];

        foreach ($allRatings as $i => $ratingRow) {
            $userIdFromHL = (int)$ratingRow['UF_USER_ID'];
            $stars = (int)$ratingRow['UF_STARS'];
            $rank = $i + 1;

            $rsUser = CUser::GetByID($userIdFromHL);
            if ($arUser = $rsUser->Fetch()) {
                $ratingRows[] = [
                    'RANK' => $rank,
                    'FULL_NAME' => $arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'],
                    'GROUP' => $arUser['UF_STATUS_GROUP'],
                    'STARS' => $stars,
                    'IS_CURRENT' => $userId === $userIdFromHL,
                ];
            }
        }
        ?>

        <section class="rating-section">
            <div class="rating-container">
                <h2 class="page-heading">Рейтинг</h2>

                <div class="rating-list">
                    <div class="rating-header-row">
                        <span class="rating-col col-place">Место</span>
                        <span class="rating-col col-name">ФИО студента</span>
                        <span class="rating-col col-group">Академ. группа</span>
                        <span class="rating-col col-program">Звезды</span>
                    </div>

                    <?php foreach ($ratingRows as $row): ?>
                        <div class="rating-row <?= $row['IS_CURRENT'] ? 'current-user' : '' ?>">
                            <span class="rating-col col-place" data-label="Место"><?= $row['RANK'] ?></span>
                            <span class="rating-col col-name" data-label="ФИО студента"><?= htmlspecialchars($row['FULL_NAME']) ?></span>
                            <span class="rating-col col-group" data-label="Академ. группа"><?= htmlspecialchars($row['GROUP']) ?></span>
                            <span class="rating-col col-program" data-label="ОП"><?= htmlspecialchars($row['STARS']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    <?else:?>
        <p>Вам необходимо <a href="/auth/">авторизоваться</a> для доступа в личный кабинет.</p>
    <?endif;?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
