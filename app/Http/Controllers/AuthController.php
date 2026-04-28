<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Регистрация нового пользователя
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $response = $this->authService->register($request->validated());

        return response()->json($response, 201);
    }

    /**
     * Авторизация пользователя
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->authService->login($request->validated());

        return response()->json($response);
    }

    /**
     * Выход пользователя
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout(auth()->user());

        return response()->json(['message' => 'Вы успешно вышли из системы']);
    }

    /**
     * Получение текущего пользователя
     */
    public function me(): JsonResponse
    {
        $user = $this->authService->getCurrentUser(auth()->user());

        return response()->json($user);
    }
}
