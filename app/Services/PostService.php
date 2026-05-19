<?php

namespace App\Services;

use App\Contracts\PostRepositoryInterface;
use App\Exceptions\PostNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;

class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {}

    /**
     * Получить список постов с фильтрацией
     *
     * @throws UnauthorizedException
     */
    public function getPosts(User $currentUser, array $filters = []): array
    {
        if (!$currentUser->hasAccess('view posts')) {
            throw new UnauthorizedException("У вас нет прав для просмотра постов!");
        }

        // Если пользователь не может просматривать все посты, показываем только его посты
        $userId = $currentUser->hasAccess('view posts') ? null : $currentUser->id;
        $posts = $this->postRepository->list($filters, $userId);

        return ['posts' => PostResource::collection($posts)];
    }

    /**
     * Получить пост по ID
     *
     * @throws PostNotFoundException
     * @throws UnauthorizedException
     */
    public function getPostById(User $currentUser, string $postId): array
    {
        $post = $this->postRepository->findById($postId);
        if (!$post) {
            throw new PostNotFoundException("Пост с ID {$postId} не найден!");
        }

        // Проверяем права: пользователь может просматривать свой пост или имеет право просматривать все посты
        if ($currentUser->id !== $post->user_id && !$currentUser->hasAccess('view posts')) {
            throw new UnauthorizedException("У вас нет прав для просмотра этого поста!");
        }

        return $this->serializePostToArray($post);
    }

    /**
     * Создать новый пост
     *
     * @throws UnauthorizedException
     */
    public function createPost(User $currentUser, array $data): array
    {
        if (!$currentUser->hasAccess('create posts')) {
            throw new UnauthorizedException("У вас нет прав для создания постов!");
        }

        $post = $this->postRepository->create($currentUser->id, $data);
        return $this->serializePostToArray($post);
    }

    /**
     * Обновить пост
     *
     * @throws PostNotFoundException
     * @throws UnauthorizedException
     */
    public function updatePost(User $currentUser, string $postId, array $data): array
    {
        $post = $this->postRepository->findById($postId);
        if (!$post) {
            throw new PostNotFoundException("Пост с ID {$postId} не найден!");
        }

        // Проверяем права: пользователь может редактировать свой пост или имеет право редактировать все посты
        if ($currentUser->id === $post->user_id && !$currentUser->hasAccess('edit own posts')) {
            throw new UnauthorizedException("У вас нет прав для редактирования своих постов!");
        }

        if ($currentUser->id !== $post->user_id && !$currentUser->hasAccess('edit all posts')) {
            throw new UnauthorizedException("У вас нет прав для редактирования этого поста!");
        }

        $this->postRepository->update($postId, $data);
        return $this->serializePostToArray($post->fresh());
    }

    /**
     * Удалить пост
     *
     * @throws PostNotFoundException
     * @throws UnauthorizedException
     */
    public function deletePost(User $currentUser, string $postId): void
    {
        $post = $this->postRepository->findById($postId);
        if (!$post) {
            throw new PostNotFoundException("Пост с ID {$postId} не найден!");
        }

        // Проверяем права: пользователь может удалять свой пост или имеет право удалять все посты
        if ($currentUser->id === $post->user_id && !$currentUser->hasAccess('delete own posts')) {
            throw new UnauthorizedException("У вас нет прав для удаления своих постов!");
        }

        if ($currentUser->id !== $post->user_id && !$currentUser->hasAccess('delete all posts')) {
            throw new UnauthorizedException("У вас нет прав для удаления этого поста!");
        }

        $this->postRepository->delete($postId);
    }

    /**
     * Получить посты конкретного пользователя
     *
     * @throws UnauthorizedException
     */
    public function getUserPosts(User $currentUser, string $userId, array $filters = []): array
    {
        if (!$currentUser->hasAccess('view posts') && $currentUser->id !== $userId) {
            throw new UnauthorizedException("У вас нет прав для просмотра постов этого пользователя!");
        }

        $posts = $this->postRepository->list($filters, $userId);

        return ['posts' => PostResource::collection($posts)];
    }

    /**
     * Сериализация поста в массив
     */
    private function serializePostToArray(Post $post): array
    {
        return ['post' => PostResource::make($post)];
    }

    /**
     * Сериализация пользователя в массив (для вложенных данных)
     */
    private function serializeUserToArray(User $user): array
    {
        return ['user' => UserResource::make($user)];
    }
}
