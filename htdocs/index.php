<?php

use NewsPortal\DTO\News;

require $_SERVER['DOCUMENT_ROOT'] . '/common/header.php';

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deleteItem') {
    \NewsPortal\Controllers\NewsController::deleteItem($_REQUEST['itemId']);
}


$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
$itemsPerPage = isset($_REQUEST['itemsPerPage']) ? $_REQUEST['itemsPerPage'] : 10;
$newsList = \NewsPortal\Controllers\NewsController::getList($page, $itemsPerPage);
?>
    <a href="/create/" class="button">Добавить новость</a>
    <div class="news-list">
        <?
        /**
         * @var $item News
         */
        foreach ($newsList as $item): ?>
            <a href="/<?= $item->Code ?>/" class="news-list__row">
                <h2 class="news-list__item-header"><?= $item->Name ?></h2>
                <div class="news-list__item-body">
                    <div class="news-list__image-wrapper">
                        <img class="news-list__image" src="<?= $item->PreviewPicture ?>">
                    </div>
                    <div class="news-list__preview-text">
                        <?= $item->PreviewText ?>
                    </div>
                </div>
                <form action="/" method="post">
                    <input type="hidden" name="action" value="deleteItem">
                    <input type="hidden" name="itemId" value="<?= $item->ID ?>">
                    <button>Удалить новость</button>
                </form>
            </a>
        <?
        endforeach; ?>
        <?
        if (!$newsList): ?>
            <h2>Новостей не найдено</h2>
        <?
        else :
            $navigation = \NewsPortal\Controllers\PageNavigationController::getNav($page, $itemsPerPage);
            if ($navigation['pages']):?>
                <div class="navigation">
                    Страницы:
                    <?
                    foreach ($navigation['pages'] as $navigationItem) :?>
                        <a class="navigation__link<?
                        if ($navigationItem['current']) echo ' navigation__link--current' ?>"
                           href="<?= $navigationItem['url'] ?>"><?= $navigationItem['num'] ?></a>
                    <?
                    endforeach; ?>
                    <a class="navigation__link navigation__link--all" href="<?= $navigation['unlimitedUrl'] ?>">Все</a>
                </div>
            <?
            endif; ?>
        <?
        endif; ?>
    </div>
<?
