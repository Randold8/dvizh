<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<footer>
    <p>
        Над реализацией проекта работали:<br>
        Баранова Ксения,<br> Зеленов Виталий,<br> Маргвелашвили Мариами,<br> Поляков Алексей,<br> Савинова Алина,<br> Сангалова Карина,<br> Софронова Елизавета<br><br>
        Нижний Новгород, 2025
    </p>
</footer>
<script src="<?= SITE_TEMPLATE_PATH?>/js/eventsProvider.js"></script>
<script src="<?= SITE_TEMPLATE_PATH?>/js/mainevents.js"></script>
<script src="<?= SITE_TEMPLATE_PATH?>/js/date.js"></script>
<script src="<?= SITE_TEMPLATE_PATH?>/js/filter.js"></script>
<?php if ($APPLICATION->GetCurDir() === '/'): ?>
<script src="<?= SITE_TEMPLATE_PATH?>/js/eventlist.js"></script>
<?php endif; ?>

<?php if ($APPLICATION->GetCurDir() === '/personal/add-event/'): ?>
<script src="<?= SITE_TEMPLATE_PATH?>/js/event-creation.js"></script>
<?php endif; ?>
<script src="<?= SITE_TEMPLATE_PATH?>/js/calendar.js"></script>
<script src="<?= SITE_TEMPLATE_PATH?>/js/scroll-links.js"></script>
</body>
</html>

