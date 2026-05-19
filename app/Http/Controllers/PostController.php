<?php

namespace App\Http\Controllers;

use App\Exceptions\PostNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Http\Requests\GetPostsRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Posts", description: "Управление публикациями")]
class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    )
    {
    }

    /**
     * @throws UnauthorizedException
     */
    #[OA\Get(
        path: "/api/posts",
        description: "Возвращает пагинированный список всех публикаций с возможностью фильтрации и сортировки",
        summary: "Получить список всех публикаций в системе",
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "sort", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["date", "title"], example: "date")),
            new OA\Parameter(name: "order", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"], example: "desc")),
            new OA\Parameter(name: "limit", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
            new OA\Parameter(name: "offset", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 0)),
            new OA\Parameter(name: "from_date", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date", example: "2024-01-01")),
            new OA\Parameter(name: "to_date", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date", example: "2024-12-31")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список публикаций",
                content: new OA\JsonContent(ref: "#/components/schemas/PostsResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            )
        ]
    )]
    public function index(GetPostsRequest $request): JsonResponse
    {
        $posts = $this->postService->getPosts(
            auth()->user(),
            $request->validated()
        );

        return response()->json($posts);
    }

    /**
     * @throws UnauthorizedException
     */
    #[OA\Get(
        path: "/api/posts/my",
        description: "Возвращает пагинированный список публикаций текущего пользователя",
        summary: "Получить список моих публикаций",
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "sort", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["date", "title"], example: "date")),
            new OA\Parameter(name: "order", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"], example: "desc")),
            new OA\Parameter(name: "limit", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
            new OA\Parameter(name: "offset", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 0)),
            new OA\Parameter(name: "from_date", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date", example: "2024-01-01")),
            new OA\Parameter(name: "to_date", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date", example: "2024-12-31")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список моих публикаций",
                content: new OA\JsonContent(ref: "#/components/schemas/PostsResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            )
        ]
    )]
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
     * @throws UnauthorizedException
     */
    #[OA\Post(
        path: "/api/posts",
        description: "Создает новую публикацию от имени текущего пользователя",
        summary: "Создать новую публикацию",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "text"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Название публикации"),
                    new OA\Property(property: "text", type: "string", example: "Текст публикации"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Публикация успешно создана",
                content: new OA\JsonContent(ref: "#/components/schemas/PostResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Ошибка валидации"
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            )
        ]
    )]
    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->createPost(
            auth()->user(),
            $request->validated()
        );

        return response()->json($post, 201);
    }

    /**
     * @throws PostNotFoundException
     * @throws UnauthorizedException
     */
    #[OA\Get(
        path: "/api/posts/{id}",
        description: "Возвращает данные публикации по её ID",
        summary: "Получить публикацию по ID",
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Данные публикации",
                content: new OA\JsonContent(ref: "#/components/schemas/PostsResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            ),
            new OA\Response(
                response: 403,
                description: "Нет прав для просмотра публикации"
            ),
            new OA\Response(
                response: 404,
                description: "Публикация не найдена"
            )
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $post = $this->postService->getPostById(auth()->user(), $id);
        return response()->json($post);
    }

    /**
     * @throws UnauthorizedException
     * @throws PostNotFoundException
     */
    #[OA\Put(
        path: "/api/posts/{id}",
        description: "Обновляет данные публикации",
        summary: "Обновить публикацию",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Новое название"),
                    new OA\Property(property: "text", type: "string", example: "Новый текст"),
                ],
                type: "object"
            )
        ),
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Публикация успешно обновлена",
                content: new OA\JsonContent(ref: "#/components/schemas/PostsResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Ошибка валидации"
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            ),
            new OA\Response(
                response: 403,
                description: "Нет прав для обновления публикации"
            ),
            new OA\Response(
                response: 404,
                description: "Публикация не найдена"
            )
        ]
    )]
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
     * @throws PostNotFoundException
     * @throws UnauthorizedException
     */
    #[OA\Delete(
        path: "/api/posts/{id}",
        description: "Удаляет публикацию по ID",
        summary: "Удалить публикацию",
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Публикация успешно удалена",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Публикация успешно удалена"),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            ),
            new OA\Response(
                response: 403,
                description: "Нет прав для удаления публикации"
            ),
            new OA\Response(
                response: 404,
                description: "Публикация не найдена"
            )
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $this->postService->deletePost(auth()->user(), $id);
        return response()->json(['message' => 'Публикация успешно удалена']);
    }
}
