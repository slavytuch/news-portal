<?php

namespace NewsPortal\Controllers;

use NewsPortal\Exceptions\NewsCreateException;
use NewsPortal\Models\News;

class NewsController
{

    /**
     * Получает список новостей
     *
     * @param int|string $page - страница постраничной навигации
     * @param int|string $itemsPerPage - количество новостей на каждую страницу постраничной навигации
     * @return array|false массив объектов \NewsPortal\DTO\News , false при ошибке выполнения запроса.
     */
    public static function getList($page = 1, $itemsPerPage = 10)
    {
        if (intval($page) != $page || $page < 1) {
            $page = 1;
        }

        if (is_int($itemsPerPage) && $itemsPerPage < 1) {
            $itemsPerPage = 1;
        }

        return News::getList($page, $itemsPerPage, $itemsPerPage == 'unlimited');
    }

    /**
     * Создаёт новость
     *
     * @param array $request массив $_REQUEST с полями создания новости
     * @return array ассоциативный массив ответа с полями error - флаг ошибки, message - текст ошибки
     */
    public static function createItem(array $request)
    {
        $response = ['error' => false, 'message' => ''];
        try {
            if (!isset($request['name']) || $request['name'] == '') {
                throw new NewsCreateException('Название новости не задано!');
            }
            $newsItem = new \NewsPortal\DTO\News();

            if (!isset($request['code']) || $request['code'] == '') {
                $request['code'] = self::translit($request['name']);
            } else {
                $request['code'] = self::translit($request['code']);
            }

            $newsItem->Name = $request['name'];
            $newsItem->Code = $request['code'];
            $newsItem->PreviewText = $request['previewText'];
            $newsItem->DetailText = $request['detailText'];
            if (isset($_FILES['previewPicture']) && $_FILES['previewPicture']['size'] > 0) {
                $newsItem->PreviewPicture = self::saveFile($_FILES['previewPicture']);
            }

            if (isset($_FILES['detailPicture']) && $_FILES['detailPicture']['size'] > 0) {
                $newsItem->DetailPicture = self::saveFile($_FILES['previewPicture']);
            }

            $errors = News::save($newsItem);

            if (is_array($errors)) {
                if (file_exists($newsItem->PreviewPicture)) {
                    unlink($newsItem->PreviewPicture);
                }
                if (file_exists($newsItem->DetailPicture)) {
                    unlink($newsItem->DetailPicture);
                }
                throw new NewsCreateException(implode('<br>', $errors));
            }
        } catch (\Exception $ex) {
            $response['error'] = true;
            $response['message'] = $ex->getMessage();
        } finally {
            return $response;
        }
    }

    /**
     * Удаляет номер по его ID
     *
     * @param int $itemId идентификатор новости
     * @return bool|array false если $itemId невалидный, true если операция успушна, array если возникла ошибка в БД
     */
    public static function deleteItem(int $itemId)
    {
        if (intval($itemId) != $itemId) {
            return false;
        }

        return News::delete(intval($itemId));
    }

    /**
     * Получает новость по её коду
     * @param string $code - код новости
     * @return bool|array|\NewsPortal\DTO\News - false если код невалидный, DTO\News при успешном поиске, array при ошибке запроса к БД
     */
    public static function getByCode(string $code)
    {
        if (!$code) {
            return false;
        }

        return News::getByCode($code);
    }

    /**
     * Транслитерирует строку в пригодный для url вид - переводит кириллицу на английский язык и удаляет/заменяет спец. символы
     *
     * @param string $name
     * @return string транслитерированный очищенный код
     */
    protected static function translit(string $name): string
    {
        $converter = array(
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ь' => '',
            'ы' => 'y',
            'ъ' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
        );

        $value = mb_strtolower($name);
        $value = strtr($value, $converter);
        $value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
        $value = mb_ereg_replace('[-]+', '-', $value);
        $value = trim($value, '-');

        return $value;
    }

    /**
     * Сохраняет файл в структуре сервера
     *
     * @param array $file - элемент из массива $_FILES
     * @return string относительный путь файла на сервере
     * @throws NewsCreateException
     */
    protected static function saveFile($file)
    {
        $randomString = self::generateRandomString();
        $fileSavePath = $_SERVER['DOCUMENT_ROOT'] . '/images/';
        if (!file_exists($fileSavePath) && !mkdir($fileSavePath)) {
            throw new NewsCreateException('Ошибка создания директории сохранения файла!');
        }
        $fileName = explode('.', $file['name']);
        $fileExtension = end($fileName);
        $tryCount = 0;
        while (file_exists($fileSavePath . $randomString . '.' . $fileExtension)) {
            $randomString = self::generateRandomString();
            if (++$tryCount > 1000) {
                throw new NewsCreateException(
                    'Рандомизатор не может сгенерировать корректный путь для сохранения файла!'
                );
            }
        }
        $fileSavePath = $fileSavePath . $randomString . '.' . $fileExtension;

        if (!copy($file['tmp_name'], $fileSavePath)) {
            throw new NewsCreateException('Ошибка копирования файла!');
        }
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $fileSavePath);
    }

    /**
     * Генерирует рандомную строку
     *
     * @param int $length - длинна строки
     * @return string рандомная строка
     */
    protected static function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}