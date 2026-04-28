<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\SyncRolesRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Получить список всех пользователей
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $users = $this->userService->getAllUsers(auth()->user(), $perPage);

        return response()->json($users);
    }

    /**
     * Создать нового пользователя
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser(
            auth()->user(),
            $request->validated()
        );

        return response()->json($user, 201);
    }

    /**
     * Получить пользователя по ID
     */
    public function show(string $id): JsonResponse
    {
        $user = $this->userService->getUserById(auth()->user(), $id);

        return response()->json($user);
    }

    /**
     * Обновить пользователя
     */
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
     * Удалить пользователя
     */
    public function destroy(string $id): JsonResponse
    {
        $this->userService->deleteUser(auth()->user(), $id);

        return response()->json(['message' => 'Пользователь успешно удален']);
    }

    /**
     * Назначить роль пользователю
     */
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
     * Переназначить роли пользователю
     */
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
