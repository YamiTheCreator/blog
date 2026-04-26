<?php

namespace App\Exceptions;

class InvalidCredentialsException extends DomainException
{
    public function __construct(string $message = "Неверные данные пользователя!")
    {
        parent::__construct($message, "invalid_credentials", 403);
    }
}
