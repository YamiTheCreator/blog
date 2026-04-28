<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $email_verified_at,
        public string $created_at,
        public string $updated_at,
        public ?array $roles = null,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            email_verified_at: $user->email_verified_at?->toISOString(),
            created_at: $user->created_at->toISOString(),
            updated_at: $user->updated_at->toISOString(),
            roles: $user->roles->pluck('name')->toArray(),
        );
    }
}
