<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Права для обычных пользователей (мобильное приложение)
        $userPermissions = [
            'view posts',      // Просмотр общей ленты публикаций
            'create posts',    // Создание своих публикаций
            'edit own posts',  // Редактирование своих публикаций
            'delete own posts', // Удаление своих публикаций
        ];

        // Права для администраторов (админка Orchid)
        $adminPermissions = [
            'access admin',    // Доступ к админке
            'view users',      // Просмотр пользователей
            'create users',    // Создание пользователей
            'edit users',      // Редактирование пользователей
            'delete users',    // Удаление пользователей
            'view all posts',  // Просмотр всех публикаций
            'edit all posts',  // Редактирование всех публикаций
            'delete all posts', // Удаление всех публикаций
        ];

        // Создаем все права
        $allPermissions = array_merge($userPermissions, $adminPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
