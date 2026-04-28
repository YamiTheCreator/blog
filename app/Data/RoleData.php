<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\Permission\Models\Role;

class RoleData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $guard_name,
        public array $permissions,
        public string $created_at,
        public string $updated_at,
    ) {}

    public static function fromModel(Role $role): self
    {
        return new self(
            id: $role->id,
            name: $role->name,
            guard_name: $role->guard_name,
            permissions: $role->permissions->pluck('name')->toArray(),
            created_at: $role->created_at->toISOString(),
            updated_at: $role->updated_at->toISOString(),
        );
    }
}
