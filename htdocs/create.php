<?php

require $_SERVER['DOCUMENT_ROOT'] . '/common/header.php';

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'create') {
    $response = \NewsPortal\Controllers\NewsController::createItem($_REQUEST);
}
?>
<?
if (isset($response)): ?>
    <?
    if ($response['error']): ?>
        <div class="color-red"><?= $response['message'] ?></div>
    <?
    else: ?>
        <div class="color-green">Новость успешно добавлена</div>
    <?
    endif; ?>
<?
endif; ?>
<h1>Добавление новости</h1>
<form class="news-add" enctype="multipart/form-data" method="post" action="/create.php">
    <input type="hidden" name="action" value="create">
    <div>
        Название:
        <input type="text" name="name" placeholder="Название новости" required>
    </div>
    <div>
        Код:
        <input type="text" name="code" placeholder="Код новости">
    </div>
    <div>
        Анонсовое изображение:
        <input type="file" name="previewPicture">
    </div>
    <div>
        Анонсовый текст:
        <textarea name="previewText"></textarea>
    </div>
    <div>
        Детальное изображение:
        <input type="file" name="detailPicture">
    </div>
    <div>
        Детальный текст:
        <textarea name="detailText"></textarea>
    </div>
    <button>Создать</button>
</form>
<a href="/">К списку новостей</a>
