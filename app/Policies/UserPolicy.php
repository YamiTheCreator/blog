<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAccess('view users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasAccess('view users');
    }

    public function create(User $user): bool
    {
        return $user->hasAccess('create users');
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasAccess('edit users');
    }

    public function delete(User $user, User $model): bool
    {
        // Пользователь не может удалить сам себя
        return $user->id !== $model->id && $user->hasAccess('delete users');
    }

    public function accessAdmin(User $user): bool
    {
        return $user->hasAccess('access admin');
    }
}
