<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function createAccessToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function findById(string $id): ?User
    {
        return User::query()->find($id);
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()->latest()->paginate($perPage);
    }

    public function update(string $id, array $data): bool
    {
        $user = $this->findById($id);
        if (!$user) return false;

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $user->update($data);
    }

    public function delete(string $id): bool
    {
        $user = $this->findById($id);
        return $user ? $user->delete() : false;
    }

    public function getQuery(): Builder
    {
        return User::query();
    }

    public function assignRole(string $userId, string $roleName): bool
    {
        $user = $this->findById($userId);
        if (!$user) {
            return false;
        }

        $user->assignRole($roleName);
        return true;
    }

    public function syncRoles(string $userId, array $roleNames): bool
    {
        $user = $this->findById($userId);
        if (!$user) {
            return false;
        }

        $user->syncRoles($roleNames);
        return true;
    }
}
