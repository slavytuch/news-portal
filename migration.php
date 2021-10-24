<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$connection = new mysqli($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);

if (mysqli_connect_errno()) {
    echo 'Ошибка подключения к базе даных: ' . mysqli_connect_error();
}

$result = $connection->query('show databases');

$existingDatabaseList = [];

while ($database = $result->fetch_row()) {
    $existingDatabaseList[] = $database[0];
}

if (!in_array($_ENV['MYSQL_DB'], $existingDatabaseList)) {
    echo 'База данных news_portal не найдена. Создание...<br>' . PHP_EOL;

    $result = $connection->query("CREATE DATABASE {$_ENV['MYSQL_DB']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    if ($result) {
        echo "База данных {$_ENV['MYSQL_DB']} создана<br>" . PHP_EOL;
    } else {
        echo 'Ошибка создания базы данных. ' . $connection->error;
        die;
    }
}
$connection->close();
$pdo = new PDO("mysql:host={$_ENV['MYSQL_HOST']};dbname={$_ENV['MYSQL_DB']}", $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
$tables = $pdo->query('show tables')->fetchAll();

$query = "CREATE TABLE `{$_ENV['MYSQL_DB']}`.`news` ( 
`ID` INT NOT NULL AUTO_INCREMENT , 
`Name` VARCHAR(300) NOT NULL , 
`Code` VARCHAR(300) NOT NULL UNIQUE , 
`PreviewText` TEXT NULL , 
`DetailText` TEXT NULL , 
`PreviewPicture` VARCHAR(300) NULL , 
`DetailPicture` VARCHAR(300) NULL , 
PRIMARY KEY (`ID`));";

if (!in_array('news', $tables)) {
    echo 'Таблица news не найдена. Создание...<br>' . PHP_EOL;
    $pdo->query($query);
    echo 'Таблица news создана.<br>' . PHP_EOL;
}
echo 'База данных и целевая таблица установлены.<br>' . PHP_EOL;