<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends DomainException
{
    public function __construct(string $message = "Пользователь не найден!")
    {
        parent::__construct($message, "user_not_found", 403);
    }
}
