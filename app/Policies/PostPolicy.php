<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Определить, может ли пользователь просматривать список постов
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view posts');
    }

    /**
     * Определить, может ли пользователь просматривать конкретный пост
     */
    public function view(User $user, Post $post): bool
    {
        return $user->hasPermissionTo('view posts');
    }

    /**
     * Определить, может ли пользователь создавать посты
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create posts');
    }

    /**
     * Определить, может ли пользователь обновлять пост
     */
    public function update(User $user, Post $post): bool
    {
        // Пользователь может редактировать свои посты или имеет право редактировать все посты (админ)
        if ($user->id === $post->user_id && $user->hasPermissionTo('edit own posts')) {
            return true;
        }

        return $user->hasPermissionTo('edit all posts');
    }

    /**
     * Определить, может ли пользователь удалять пост
     */
    public function delete(User $user, Post $post): bool
    {
        // Пользователь может удалять свои посты или имеет право удалять все посты (админ)
        if ($user->id === $post->user_id && $user->hasPermissionTo('delete own posts')) {
            return true;
        }

        return $user->hasPermissionTo('delete all posts');
    }
}
