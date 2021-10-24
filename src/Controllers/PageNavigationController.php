<?php

namespace NewsPortal\Controllers;

use NewsPortal\Models\News;

class PageNavigationController
{

    /**
     * Создаёт массив постраничной навигации
     *
     * @param int|string $currentPage текущее значение постраничной навигации
     * @param int|string $itemsPerPage количество записей на страницу постраничной навигации
     * @return array ассоциативный массив для вывода постраничной навигации
     */
    public static function getNav($currentPage, $itemsPerPage = 10)
    {
        $rowCount = News::getTotalRowNum();
        $result = [
            'totalRowCount' => $rowCount,
            'pageCount' => $itemsPerPage == 'unlimited' ? 1 : ceil($rowCount / $itemsPerPage),
            'pages' => [],
        ];

        if ($result['pageCount'] < 2) {
            return $result;
        }

        $url = $_SERVER['REQUEST_URI'];

        $url_parts = parse_url($url);
        if (isset($url_parts['query'])) {
            parse_str($url_parts['query'], $params);
        } else {
            $params = array();
        }

        for ($page = 1; $page <= $result['pageCount']; ++$page) {
            $params['page'] = $page;
            $url_parts['query'] = http_build_query($params);
            $result['pages'][] = [
                'num' => $page,
                'current' => $currentPage ? $currentPage == $page : $page == 1,
                'url' => self::buildUrl($url_parts),
            ];
        }

        unset($params['page']);
        $params['itemsPerPage'] = 'unlimited';
        $url_parts['query'] = http_build_query($params);
        $result['unlimitedUrl'] = self::buildUrl($url_parts);

        return $result;
    }

    /**
     * Строит путь к странице на основании массива, который был получен функцией parse_url
     *
     * @param array $parts - массив из parse_url
     * @return string путь из исходного массива
     */
    protected static function buildUrl(array $parts)
    {
        $url = '';
        if (!empty($parts['scheme'])) {
            $url .= $parts['scheme'] . ':';
        }
        if (!empty($parts['user']) || !empty($parts['host'])) {
            $url .= '//';
        }
        if (!empty($parts['user'])) {
            $url .= $parts['user'];
        }
        if (!empty($parts['pass'])) {
            $url .= ':' . $parts['pass'];
        }
        if (!empty($parts['user'])) {
            $url .= '@';
        }
        if (!empty($parts['host'])) {
            $url .= $parts['host'];
        }
        if (!empty($parts['port'])) {
            $url .= ':' . $parts['port'];
        }
        if (!empty($parts['path'])) {
            $url .= $parts['path'];
        }
        if (!empty($parts['query'])) {
            if (is_array($parts['query'])) {
                $url .= '?' . http_build_query($parts['query']);
            } else {
                $url .= '?' . $parts['query'];
            }
        }
        if (!empty($parts['fragment'])) {
            $url .= '#' . $parts['fragment'];
        }

        return $url;
    }
}