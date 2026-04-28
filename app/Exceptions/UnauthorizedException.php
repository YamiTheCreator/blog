<?php

namespace App\Exceptions;

class UnauthorizedException extends DomainException
{
    public function __construct(string $message = "Недостаточно прав для выполнения операции!")
    {
        parent::__construct($message, "unauthorized", 403);
    }
}
