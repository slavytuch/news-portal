<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
?>
<head>
    <link rel="stylesheet" href="/common/style.css">
    <title>Новостной портал</title>
</head>
<body>
<div class="wrapper">
