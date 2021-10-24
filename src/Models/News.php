<?php

namespace NewsPortal\Models;

use NewsPortal\Database\Connection;
use PDO;

class News
{
    /**
     * Получает список новостей
     *
     * @param int $page страница постраничной навигации
     * @param int|string $itemsPerPage количество новостей на страницу постраничной навигации
     * @param bool $unlimited флаг вывода всех новостей
     * @return array|false массив объектов \NewsPortal\DTO\News или false при ошибке запроса.
     */
    public static function getList(int $page, $itemsPerPage, bool $unlimited = false)
    {
        $offset = $unlimited ? 0 : ($page - 1) * $itemsPerPage;
        $limit = $unlimited || !$itemsPerPage ? false : $itemsPerPage;

        $query = 'SELECT ID, Name, Code, PreviewText, DetailText, PreviewPicture, DetailPicture
 FROM `news`';
        if ($limit) {
            $query .= ' LIMIT ' . $offset . ', ' . $limit;
        }
        $request = Connection::getInstance()->pdo->prepare($query);
        $request->execute();
        return $request->fetchAll(PDO::FETCH_CLASS, \NewsPortal\DTO\News::class);
    }

    /**
     * Получает новость по её коду
     *
     * @param string $code код новости
     * @return \NewsPortal\DTO\News|bool возвращает новость при успешном запросе, false при ошибке запроса
     */
    public static function getByCode(string $code)
    {
        $query = 'SELECT ID, Name, Code, PreviewText, DetailText, PreviewPicture, DetailPicture
        FROM `news` WHERE Code = :code';
        $request = Connection::getInstance()->pdo->prepare($query);
        $request->setFetchMode(PDO::FETCH_CLASS, \NewsPortal\DTO\News::class);
        $request->execute([':code' => $code]);
        return $request->fetch();
    }

    /**
     * Создаёт новость в БД
     *
     * @param \NewsPortal\DTO\News $item объект создаваемой новости с заполенными полями
     * @return bool|array при успешном добавлении возвращает true, при ошибке запроса - массив ошибки
     */
    public static function save(\NewsPortal\DTO\News $item)
    {
        $itemArray = (array)$item;
        foreach ($itemArray as $key => $value) {
            if (!$value) {
                unset($itemArray[$key]);
            }
        }

        $query = 'INSERT INTO `news` (`' . implode('`,`', array_keys($itemArray)) . '`)
        VALUES (\'' . implode('\',\'', $itemArray) . '\')';
        $request = Connection::getInstance()->pdo->prepare($query);
        $request->execute();
        if ($request->errorCode() !== '00000') {
            return $request->errorInfo();
        }
        return true;
    }

    /**
     * Удаляет новость по её идентификатору
     *
     * @param int $id идентификатор новости
     * @return bool|array true при успешной операции, массив ошибок при ошибке запроса к БД
     */
    public static function delete(int $id)
    {
        $query = "DELETE FROM `news` WHERE ID = " . $id;
        $request = Connection::getInstance()->pdo->prepare($query);
        $request->execute();
        if ($request->errorCode() !== '00000') {
            return $request->errorInfo();
        }
        return true;
    }

    /**
     * Получает количество строк в таблице news
     *
     * @return string|false количество записей в таблице news или false при ошибке запроса к БД
     */
    public static function getTotalRowNum()
    {
        $query = 'SELECT COUNT(*) from `news`';
        $request = Connection::getInstance()->pdo->prepare($query);
        $request->execute();
        $response = $request->fetch();
        if (is_array($response)) {
            return $response['COUNT(*)'];
        }
        return $response;
    }
}