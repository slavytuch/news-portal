<?php

namespace NewsPortal\Database;

class Connection
{
    private static ?Connection $instance;
    public ?\PDO $pdo;

    /**
     * Получает объект подключения к БД
     *
     * @return Connection
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->pdo = new \PDO(
            "mysql:host={$_ENV['MYSQL_HOST']};dbname={$_ENV['MYSQL_DB']}",
            $_ENV['MYSQL_USER'],
            $_ENV['MYSQL_PASSWORD']
        );
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    private function __clone()
    {
    }
}