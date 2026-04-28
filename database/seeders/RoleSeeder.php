<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем роли
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $user = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        // Назначаем права ролям
        
        // Admin - полный доступ к админке и управлению всем
        $admin->syncPermissions([
            'access admin',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view posts',
            'view all posts',
            'create posts',
            'edit own posts',
            'edit all posts',
            'delete own posts',
            'delete all posts',
        ]);

        // User - базовые права для мобильного приложения
        $user->syncPermissions([
            'view posts',
            'create posts',
            'edit own posts',
            'delete own posts',
        ]);
    }
}
