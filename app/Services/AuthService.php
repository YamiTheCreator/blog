<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Data\AuthResponseData;
use App\Data\UserData;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserAlreadyExistsException;
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
    public function register(array $data): AuthResponseData
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

        return new AuthResponseData(
            user: UserData::fromModel($user->fresh()),
            access_token: $token,
        );
    }

    /**
     * Авторизация пользователя
     *
     * @throws InvalidCredentialsException
     */
    public function login(array $credentials): AuthResponseData
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException("Неверный email или пароль!");
        }

        // Создаем токен
        $token = $this->userRepository->createAccessToken($user);

        return new AuthResponseData(
            user: UserData::fromModel($user),
            access_token: $token,
        );
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
    public function getCurrentUser(User $user): UserData
    {
        return UserData::fromModel($user);
    }
}
