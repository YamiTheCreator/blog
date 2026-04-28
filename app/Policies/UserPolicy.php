<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Определить, может ли пользователь просматривать список пользователей
     * Только администраторы в админке
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view users');
    }

    /**
     * Определить, может ли пользователь просматривать конкретного пользователя
     * Пользователь может просматривать свой профиль или админ может просматривать всех
     */
    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasPermissionTo('view users');
    }

    /**
     * Определить, может ли пользователь создавать пользователей
     * Только администраторы в админке
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create users');
    }

    /**
     * Определить, может ли пользователь обновлять пользователя
     * Пользователь может редактировать свой профиль или админ может редактировать всех
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasPermissionTo('edit users');
    }

    /**
     * Определить, может ли пользователь удалять пользователя
     * Только администраторы в админке
     */
    public function delete(User $user, User $model): bool
    {
        // Пользователь не может удалить сам себя
        return $user->id !== $model->id && $user->hasPermissionTo('delete users');
    }

    /**
     * Определить, может ли пользователь получить доступ к админке
     */
    public function accessAdmin(User $user): bool
    {
        return $user->hasPermissionTo('access admin');
    }
}
