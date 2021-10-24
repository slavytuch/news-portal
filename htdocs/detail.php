<?php

use NewsPortal\DTO\News;

require $_SERVER['DOCUMENT_ROOT'] . '/common/header.php';

/**
 * @var $newsItem News
 */
$newsItem = \NewsPortal\Controllers\NewsController::getByCode($_REQUEST['code']);
if (!$newsItem) {
    echo "Новость с кодом {$_REQUEST['code']} не найдена.";
} else { ?>

    <div class="news-detail">
        <h1><?= $newsItem->Name ?></h1>
        <img class="news-detail__image" src="<?= $newsItem->DetailPicture ?>">
        <div class="news-detail__body">
            <?= $newsItem->DetailText ?>
        </div>
        <div>
            <a href="/">К списку новостей</a>
        </div>
    </div>

    <?php
}
require $_SERVER['DOCUMENT_ROOT'] . '/common/footer.php';