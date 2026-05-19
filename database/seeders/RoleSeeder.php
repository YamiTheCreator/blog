<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем роль администратора
        $admin = Role::query()->firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'permissions' => [
                    'access admin' => true,
                    'platform.index' => true,
                    'platform.systems.users' => true,
                    'platform.systems.roles' => true,
                ],
            ]
        );

        // Создаем роль пользователя
        $user = Role::query()->firstOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'User',
                'permissions' => [
                    'view posts' => true,
                    'create posts' => true,
                    'edit own posts' => true,
                    'delete own posts' => true,
                ],
            ]
        );
    }
}
