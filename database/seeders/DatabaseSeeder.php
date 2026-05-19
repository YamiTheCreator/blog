<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Orchid\Platform\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Сначала создаем роли
        $this->call([
            RoleSeeder::class,
        ]);

        // Создаем админа если его еще нет
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->addRole(Role::query()->where('slug', 'admin')->first());

        // Создаем тестового пользователя если его еще нет
        $testUser = User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
        $testUser->addRole(Role::query()->where('slug', 'user')->first());

        // Создаем посты
        $this->call([
            PostSeeder::class,
        ]);
    }
}
