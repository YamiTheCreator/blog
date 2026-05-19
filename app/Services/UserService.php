<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Exceptions\RoleNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\UserNotFoundException;
use App\Http\Resources\UserResource;
use App\Models\User;
use Orchid\Platform\Models\Role;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    )
    {
    }

    /**
     * Получить список всех пользователей с пагинацией
     *
     * @throws UnauthorizedException
     */
    public function getAllUsers(User $currentUser, int $perPage = 15): array
    {
        if (!$currentUser->hasAccess('view users')) {
            throw new UnauthorizedException("У вас нет прав для просмотра пользователей!");
        }

        $paginator = $this->userRepository->getAllPaginated($perPage);

        return ['users' => UserResource::collection($paginator)];
    }

    /**
     * Получить пользователя по ID
     *
     * @throws UserNotFoundException
     * @throws UnauthorizedException
     */
    public function getUserById(User $currentUser, string $userId): array
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException("Пользователь с ID {$userId} не найден!");
        }

        // Проверяем права: пользователь может просматривать свой профиль или имеет право просматривать пользователей
        if ($currentUser->id !== $user->id && !$currentUser->hasAccess('view users')) {
            throw new UnauthorizedException("У вас нет прав для просмотра этого пользователя!");
        }

        return $this->serializeUserToArray($user);
    }

    /**
     * Создать нового пользователя
     *
     * @throws UserAlreadyExistsException
     * @throws UnauthorizedException
     */
    public function createUser(User $currentUser, array $data): array
    {
        if (!$currentUser->hasAccess('create users')) {
            throw new UnauthorizedException("У вас нет прав для создания пользователей!");
        }

        // Проверяем, существует ли пользователь с таким email
        if ($this->userRepository->findByEmail($data['email'])) {
            throw new UserAlreadyExistsException("Пользователь с email {$data['email']} уже существует!");
        }

        $user = $this->userRepository->create($data);
        // Назначаем роль по умолчанию, если не указана
        if (isset($data['role'])) {
            $this->assignRoleToUser($currentUser, $user->id, $data['role']);
        } else {
            $this->userRepository->assignRole($user->id, 'user');
        }

        return $this->serializeUserToArray($user->fresh());
    }

    /**
     * Обновить пользователя
     *
     * @throws UserNotFoundException
     * @throws UnauthorizedException
     */
    public function updateUser(User $currentUser, string $userId, array $data): array
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException("Пользователь с ID {$userId} не найден!");
        }

        // Проверяем права: пользователь может редактировать свой профиль или имеет право редактировать пользователей
        if ($currentUser->id !== $user->id && !$currentUser->hasAccess('edit users')) {
            throw new UnauthorizedException("У вас нет прав для редактирования этого пользователя!");
        }

        $this->userRepository->update($userId, $data);
        return $this->serializeUserToArray($user->fresh());
    }

    /**
     * Удалить пользователя
     *
     * @throws UserNotFoundException
     * @throws UnauthorizedException
     */
    public function deleteUser(User $currentUser, string $userId): void
    {
        if (!$currentUser->hasAccess('delete users')) {
            throw new UnauthorizedException("У вас нет прав для удаления пользователей!");
        }

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException("Пользователь с ID {$userId} не найден!");
        }

        // Нельзя удалить самого себя
        if ($currentUser->id === $userId) {
            throw new UnauthorizedException("Вы не можете удалить самого себя!");
        }

        $this->userRepository->delete($userId);
    }

    /**
     * Назначить роль пользователю
     * Только администраторы могут назначать роли
     *
     * @throws UserNotFoundException
     * @throws RoleNotFoundException
     * @throws UnauthorizedException
     */
    public function assignRoleToUser(User $currentUser, string $userId, string $roleName): array
    {
        // Только администраторы могут назначать роли
        if (!$currentUser->inRole('admin')) {
            throw new UnauthorizedException("Только администраторы могут назначать роли!");
        }

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException("Пользователь с ID {$userId} не найден!");
        }

        // Проверяем, существует ли роль
        if (!Role::query()->where('slug', $roleName)->exists()) {
            throw new RoleNotFoundException("Роль {$roleName} не найдена!");
        }

        $this->userRepository->assignRole($userId, $roleName);
        return $this->serializeUserToArray($user->fresh());
    }

    /**
     * Переназначить роли пользователю (заменить все роли)
     * Только администраторы могут назначать роли
     *
     * @throws UserNotFoundException
     * @throws RoleNotFoundException
     * @throws UnauthorizedException
     */
    public function syncUserRoles(User $currentUser, string $userId, array $roleNames): array
    {
        // Только администраторы могут назначать роли
        if (!$currentUser->inRole('admin')) {
            throw new UnauthorizedException("Только администраторы могут назначать роли!");
        }

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException("Пользователь с ID {$userId} не найден!");
        }

        // Проверяем, существуют ли все роли
        foreach ($roleNames as $roleName) {
            if (!Role::query()->where('slug', $roleName)->exists()) {
                throw new RoleNotFoundException("Роль {$roleName} не найдена!");
            }
        }

        $this->userRepository->syncRoles($userId, $roleNames);
        return $this->serializeUserToArray($user->fresh());
    }

    /**
     * Сериализация пользователя в массив
     */
    private function serializeUserToArray(User $user): array
    {
        return ['user' => UserResource::make($user)];
    }
}
