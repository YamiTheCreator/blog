<?php

namespace App\Exceptions;

class PostNotFoundException extends DomainException
{
    public function __construct(string $message = "Пост не найден!")
    {
        parent::__construct($message, "post_not_found", 404);
    }
}
