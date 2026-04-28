<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetPostsRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     * Получить список всех публикаций в системе
     */
    public function index(GetPostsRequest $request): JsonResponse
    {
        $posts = $this->postService->getPosts(
            auth()->user(),
            $request->validated()
        );

        return response()->json($posts);
    }

    /**
     * Получить список моих публикаций
     */
    public function myPosts(GetPostsRequest $request): JsonResponse
    {
        $posts = $this->postService->getUserPosts(
            auth()->user(),
            auth()->id(),
            $request->validated()
        );

        return response()->json($posts);
    }

    /**
     * Создать новую публикацию
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->createPost(
            auth()->user(),
            $request->validated()
        );

        return response()->json($post, 201);
    }

    /**
     * Получить публикацию по ID
     */
    public function show(string $id): JsonResponse
    {
        $post = $this->postService->getPostById(auth()->user(), $id);

        return response()->json($post);
    }

    /**
     * Обновить публикацию
     */
    public function update(UpdatePostRequest $request, string $id): JsonResponse
    {
        $post = $this->postService->updatePost(
            auth()->user(),
            $id,
            $request->validated()
        );

        return response()->json($post);
    }

    /**
     * Удалить публикацию
     */
    public function destroy(string $id): JsonResponse
    {
        $this->postService->deletePost(auth()->user(), $id);

        return response()->json(['message' => 'Публикация успешно удалена']);
    }
}
