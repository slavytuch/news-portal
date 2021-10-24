<?php

namespace NewsPortal\Exceptions;

use Throwable;

class NewsCreateException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = 'Ошибка создания новости: ' . $message;
        parent::__construct($message, $code, $previous);
    }
}