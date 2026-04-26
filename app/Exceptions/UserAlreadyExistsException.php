<?php

namespace App\Exceptions;

use Exception;

class UserAlreadyExistsException extends DomainException
{
    public function __construct(string $message = "Пользователь уже существует!")
    {
        parent::__construct($message, "user_already_exists", 403);
    }
}
