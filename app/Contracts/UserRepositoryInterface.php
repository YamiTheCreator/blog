<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function createAccessToken(User $user): string;

    public function findById(string $id): ?User;

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    public function update(string $id, array $data): bool;

    public function delete(string $id): bool;

    public function getQuery(): Builder;

    public function assignRole(string $userId, string $roleName): bool;

    public function syncRoles(string $userId, array $roleNames): bool;
}
