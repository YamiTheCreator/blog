<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "API для блога с авторизацией и публикациями",
    title: "Blog API"
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    description: "Используйте Bearer токен из Sanctum",
    scheme: "bearer"
)]
#[OA\Tag(
    name: "Auth",
    description: "Аутентификация и регистрация"
)]
#[OA\Tag(
    name: "Users",
    description: "Управление пользователями"
)]
#[OA\Tag(
    name: "Posts",
    description: "Управление публикациями"
)]
#[OA\Schema(
    schema: "UserData",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid", example: "019dd696-7ce8-73b1-a8b7-28c477e43b7e"),
        new OA\Property(property: "name", type: "string", example: "John Doe"),
        new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
        new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string"), example: ["admin"]),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-01T00:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-01T00:00:00Z"),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "PostData",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid", example: "019dd696-7ce8-73b1-a8b7-28c477e43b7e"),
        new OA\Property(property: "title", type: "string", example: "Название публикации"),
        new OA\Property(property: "text", type: "string", example: "Текст публикации"),
        new OA\Property(property: "user", ref: "#/components/schemas/UserData"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-01T00:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-01T00:00:00Z"),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "AuthResponse",
    properties: [
        new OA\Property(property: "access_token", type: "string", example: "1|AbCdEfGhIjKlMnOpQrStUvWxYz"),
        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
        new OA\Property(property: "user", ref: "#/components/schemas/UserData"),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "ValidationError",
    properties: [
        new OA\Property(property: "status", type: "integer", example: 400),
        new OA\Property(property: "title", type: "string", example: "Validation Error"),
        new OA\Property(property: "detail", type: "string", example: "The given data was invalid."),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "UnauthorizedError",
    properties: [
        new OA\Property(property: "status", type: "integer", example: 401),
        new OA\Property(property: "title", type: "string", example: "Unauthorized"),
        new OA\Property(property: "detail", type: "string", example: "Invalid credentials."),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "ConflictError",
    properties: [
        new OA\Property(property: "status", type: "integer", example: 409),
        new OA\Property(property: "title", type: "string", example: "Conflict"),
        new OA\Property(property: "detail", type: "string", example: "User already exists."),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "NotFoundError",
    properties: [
        new OA\Property(property: "status", type: "integer", example: 404),
        new OA\Property(property: "title", type: "string", example: "Not Found"),
        new OA\Property(property: "detail", type: "string", example: "Resource not found."),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "ForbiddenError",
    properties: [
        new OA\Property(property: "status", type: "integer", example: 403),
        new OA\Property(property: "title", type: "string", example: "Forbidden"),
        new OA\Property(property: "detail", type: "string", example: "Access denied."),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "ErrorResponse",
    properties: [
        new OA\Property(property: "status", type: "integer", example: 500),
        new OA\Property(property: "title", type: "string", example: "Internal Server Error"),
        new OA\Property(property: "detail", type: "string", example: "An unexpected error occurred."),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "UserResponse",
    properties: [
        new OA\Property(property: "user", ref: "#/components/schemas/UserData"),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "PostResponse",
    properties: [
        new OA\Property(property: "post", ref: "#/components/schemas/PostData"),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "UsersResponse",
    properties: [
        new OA\Property(property: "users", type: "array", items: new OA\Items(ref: "#/components/schemas/UserData")),
        new OA\Property(property: "current_page", type: "integer", example: 1),
        new OA\Property(property: "per_page", type: "integer", example: 15),
        new OA\Property(property: "total", type: "integer", example: 100),
        new OA\Property(property: "last_page", type: "integer", example: 7),
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "PostsResponse",
    properties: [
        new OA\Property(property: "posts", type: "array", items: new OA\Items(ref: "#/components/schemas/PostData")),
    ],
    type: "object"
)]
abstract class Controller
{
    //
}
