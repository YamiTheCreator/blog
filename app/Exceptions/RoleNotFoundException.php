<?php

namespace App\Exceptions;

class RoleNotFoundException extends DomainException
{
    public function __construct(string $message = "Роль не найдена!")
    {
        parent::__construct($message, "role_not_found", 404);
    }
}
