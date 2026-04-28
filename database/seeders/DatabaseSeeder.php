<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Сначала создаем права доступа и роли
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // Создаем админа если его еще нет
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Создаем тестового пользователя если его еще нет
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
        $testUser->assignRole('user');

        // Создаем посты
        $this->call([
            PostSeeder::class,
        ]);
    }
}
