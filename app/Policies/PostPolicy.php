<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAccess('view posts');
    }

    public function view(User $user, Post $post): bool
    {
        return $user->hasAccess('view posts');
    }

    public function create(User $user): bool
    {
        return $user->hasAccess('create posts');
    }

    public function update(User $user, Post $post): bool
    {
        // Пользователь может редактировать свои посты или имеет право редактировать все посты (админ)
        if ($user->id === $post->user_id && $user->hasAccess('edit own posts')) {
            return true;
        }

        return $user->hasAccess('edit all posts');
    }

    public function delete(User $user, Post $post): bool
    {
        // Пользователь может удалять свои посты или имеет право удалять все посты (админ)
        if ($user->id === $post->user_id && $user->hasAccess('delete own posts')) {
            return true;
        }

        return $user->hasAccess('delete all posts');
    }
}
