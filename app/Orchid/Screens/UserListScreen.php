<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class UserListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'users' => User::filters()
                ->defaultSort('created_at', 'desc')
                ->paginate(15),
        ];
    }

    public function name(): ?string
    {
        return 'Управление пользователями';
    }

    public function description(): ?string
    {
        return 'Список всех пользователей системы';
    }

    public function permission(): ?iterable
    {
        return [
            'access admin',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Создать пользователя')
                ->icon('plus')
                ->route('platform.systems.users.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('users', [
                TD::make('name', 'Имя')
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn (User $user) => Link::make($user->name)
                        ->route('platform.systems.users.edit', $user)),

                TD::make('email', 'Email')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),

                TD::make('roles', 'Роли')
                    ->render(fn (User $user) => $user->roles->pluck('name')->implode(', ')),

                TD::make('created_at', 'Дата создания')
                    ->sort()
                    ->render(fn (User $user) => $user->created_at->format('d.m.Y H:i')),

                TD::make('updated_at', 'Обновлено')
                    ->sort()
                    ->render(fn (User $user) => $user->updated_at->format('d.m.Y H:i')),
            ]),
        ];
    }
}
