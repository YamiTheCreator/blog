<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserAlreadyExistsException;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Регистрация нового пользователя
     *
     * @throws UserAlreadyExistsException
     */
    public function register(array $data): array
    {
        // Проверяем, существует ли пользователь с таким email
        if ($this->userRepository->findByEmail($data['email'])) {
            throw new UserAlreadyExistsException("Пользователь с email {$data['email']} уже существует!");
        }

        // Создаем пользователя
        $user = $this->userRepository->create($data);
        // Назначаем роль по умолчанию
        $this->userRepository->assignRole($user->id, 'user');
        // Создаем токен
        $token = $this->userRepository->createAccessToken($user);

        return $this->serializeResponseToArray($user->fresh(), $token);
    }

    /**
     * Авторизация пользователя
     *
     * @throws InvalidCredentialsException
     */
    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException("Неверный email или пароль!");
        }

        // Создаем токен
        $token = $this->userRepository->createAccessToken($user);

        return $this->serializeResponseToArray($user, $token);
    }

    /**
     * Выход пользователя (удаление токенов)
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Получение текущего пользователя
     */
    public function getCurrentUser(User $user): array
    {
        return $this->serializeResponseToArray($user);
    }

    /**
     * Сериализация ответа в массив
     */
    private function serializeResponseToArray(User $user, ?string $token = null): array
    {
        $response = [
            'user' => UserResource::make($user),
        ];

        if ($token) {
            $response['access_token'] = $token;
            $response['token_type'] = 'Bearer';
        }

        return $response;
    }
}
