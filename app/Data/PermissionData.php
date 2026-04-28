<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\Permission\Models\Permission;

class PermissionData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $guard_name,
        public string $created_at,
        public string $updated_at,
    ) {}

    public static function fromModel(Permission $permission): self
    {
        return new self(
            id: $permission->id,
            name: $permission->name,
            guard_name: $permission->guard_name,
            created_at: $permission->created_at->toISOString(),
            updated_at: $permission->updated_at->toISOString(),
        );
    }
}
