<?php

namespace App\Exceptions;

class ValidationException extends DomainException
{
    public function __construct(string $message = "Ошибка валидации данных!")
    {
        parent::__construct($message, "validation_error", 422);
    }
}
