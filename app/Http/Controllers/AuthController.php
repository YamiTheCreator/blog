<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Auth", description: "Аутентификация и регистрация")]
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    )
    {
    }

    #[OA\Post(
        path: "/api/auth/register",
        description: "Создает нового пользователя и возвращает токен доступа",
        summary: "Регистрация нового пользователя",
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
                content: new OA\JsonContent(ref: "#/components/schemas/AuthResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")
            ),
            new OA\Response(
                response: 409,
                description: "Пользователь с таким email уже существует",
                content: new OA\JsonContent(ref: "#/components/schemas/ConflictError")
            )
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $response = $this->authService->register($request->validated());
        return response()->json($response, 201);
    }

    #[OA\Post(
        path: "/api/auth/login",
        description: "Аутентифицирует пользователя и возвращает токен доступа",
        summary: "Авторизация пользователя",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешная авторизация",
                content: new OA\JsonContent(ref: "#/components/schemas/AuthResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неверные учетные данные",
                content: new OA\JsonContent(ref: "#/components/schemas/UnauthorizedError")
            )
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        dd("");
        $response = $this->authService->login($request->validated());
        return response()->json($response);
    }

    #[OA\Post(
        path: "/api/auth/logout",
        description: "Удаляет токен доступа пользователя",
        summary: "Выход пользователя",
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный выход",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Вы успешно вышли из системы"),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            )
        ]
    )]
    public function logout(): JsonResponse
    {
        $this->authService->logout(auth()->user());
        return response()->json(['message' => 'Вы успешно вышли из системы']);
    }

    #[OA\Get(
        path: "/api/auth/me",
        description: "Возвращает данные текущего аутентифицированного пользователя",
        summary: "Получение текущего пользователя",
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Данные пользователя",
                content: new OA\JsonContent(ref: "#/components/schemas/UserResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизованный запрос"
            )
        ]
    )]
    public function me(): JsonResponse
    {
        $user = $this->authService->getCurrentUser(auth()->user());
        return response()->json($user);
    }
}
