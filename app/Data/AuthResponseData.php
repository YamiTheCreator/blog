<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class AuthResponseData extends Data
{
    public function __construct(
        public UserData $user,
        public string $access_token,
        public string $token_type = 'Bearer',
    ) {}
}
