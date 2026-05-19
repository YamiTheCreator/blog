<?php

namespace App\Http\Controllers;

use App\Exceptions\RoleNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\UserNotFoundException;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\SyncRolesRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Users", description: "Управление пользователями")]
class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    #[OA\Get(
        path: "/api/users",
        description: "Возвращает пагинированный список всех пользователей",
        summary: "Получить список всех пользователей",
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список пользователей",
                content: new OA\JsonContent(ref: "#/components/schemas/UsersResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            ),
            new OA\Response(
                response: 403,
                description: "Нет прав для просмотра пользователей"
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $users = $this->userService->getAllUsers(auth()->user(), $perPage);
        return response()->json($users);
    }

    /**
     * @throws UnauthorizedException
     * @throws UserAlreadyExistsException
     */
    #[OA\Post(
        path: "/api/users",
        description: "Создает нового пользователя с указанными данными",
        summary: "Создать нового пользователя",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Пользователь успешно создан",
                content: new OA\JsonContent(ref: "#/components/schemas/UserResponse")
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
                description: "Нет прав для создания пользователей"
            )
        ]
    )]
    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser(
            auth()->user(),
            $request->validated()
        );

        return response()->json($user, 201);
    }

    /**
     * @throws UnauthorizedException
     * @throws UserNotFoundException
     */
    #[OA\Get(
        path: "/api/users/{id}",
        description: "Возвращает данные пользователя по его ID",
        summary: "Получить пользователя по ID",
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Данные пользователя",
                content: new OA\JsonContent(ref: "#/components/schemas/UserResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            ),
            new OA\Response(
                response: 403,
                description: "Нет прав для просмотра пользователя"
            ),
            new OA\Response(
                response: 404,
                description: "Пользователь не найден"
            )
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $user = $this->userService->getUserById(auth()->user(), $id);
        return response()->json($user);
    }

    /**
     * @throws UnauthorizedException
     * @throws UserNotFoundException
     */
    #[OA\Put(
        path: "/api/users/{id}",
        description: "Обновляет данные пользователя",
        summary: "Обновить пользователя",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
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
                description: "Пользователь успешно обновлен",
                content: new OA\JsonContent(ref: "#/components/schemas/UserResponse")
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
                description: "Нет прав для обновления пользователя"
            ),
            new OA\Response(
                response: 404,
                description: "Пользователь не найден"
            )
        ]
    )]
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->updateUser(
            auth()->user(),
            $id,
            $request->validated()
        );

        return response()->json($user);
    }

    /**
     * @throws UnauthorizedException
     * @throws UserNotFoundException
     */
    #[OA\Delete(
        path: "/api/users/{id}",
        description: "Удаляет пользователя по ID",
        summary: "Удалить пользователя",
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Пользователь успешно удален",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Пользователь успешно удален"),
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
                description: "Нет прав для удаления пользователя"
            ),
            new OA\Response(
                response: 404,
                description: "Пользователь не найден"
            )
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $this->userService->deleteUser(auth()->user(), $id);
        return response()->json(['message' => 'Пользователь успешно удален']);
    }

    /**
     * @throws UnauthorizedException
     * @throws RoleNotFoundException
     * @throws UserNotFoundException
     */
    #[OA\Post(
        path: "/api/users/{id}/assign-role",
        description: "Назначает одну роль пользователю",
        summary: "Назначить роль пользователю",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["role"],
                properties: [
                    new OA\Property(property: "role", type: "string", example: "admin"),
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
                description: "Роль успешно назначена",
                content: new OA\JsonContent(ref: "#/components/schemas/UserResponse")
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
                description: "Нет прав для назначения ролей"
            ),
            new OA\Response(
                response: 404,
                description: "Пользователь не найден"
            )
        ]
    )]
    public function assignRole(AssignRoleRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->assignRoleToUser(
            auth()->user(),
            $id,
            $request->validated()['role']
        );

        return response()->json($user);
    }

    /**
     * @throws UnauthorizedException
     * @throws UserNotFoundException
     * @throws RoleNotFoundException
     */
    #[OA\Post(
        path: "/api/users/{id}/sync-roles",
        description: "Синхронизирует роли пользователя (удаляет старые, добавляет новые)",
        summary: "Переназначить роли пользователю",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["roles"],
                properties: [
                    new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string"), example: ["admin", "user"]),
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
                description: "Роли успешно синхронизированы",
                content: new OA\JsonContent(ref: "#/components/schemas/UserResponse")
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
                description: "Нет прав для синхронизации ролей"
            ),
            new OA\Response(
                response: 404,
                description: "Пользователь не найден"
            )
        ]
    )]
    public function syncRoles(SyncRolesRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->syncUserRoles(
            auth()->user(),
            $id,
            $request->validated()['roles']
        );

        return response()->json($user);
    }
}
