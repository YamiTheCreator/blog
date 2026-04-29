<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserEditScreen extends Screen
{
    public ?User $user = null;

    public function query(User $user): iterable
    {
        return [
            'user' => $user,
        ];
    }

    public function name(): ?string
    {
        return $this->user->exists ? 'Редактировать пользователя' : 'Создать пользователя';
    }

    public function description(): ?string
    {
        return 'Управление данными пользователя';
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
            Button::make('Сохранить')
                ->icon('check')
                ->method('save')
                ->type(Color::SUCCESS),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->type(Color::DANGER)
                ->confirm('Вы уверены, что хотите удалить этого пользователя?')
                ->canSee($this->user->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('user.name')
                    ->title('Имя')
                    ->placeholder('Введите имя пользователя')
                    ->required(),

                Input::make('user.email')
                    ->title('Email')
                    ->type('email')
                    ->placeholder('Введите email')
                    ->required(),

                Password::make('user.password')
                    ->title('Пароль')
                    ->placeholder($this->user->exists ? 'Оставьте пустым, чтобы не менять' : 'Введите пароль')
                    ->required(!$this->user->exists),

                Relation::make('user.roles.')
                    ->title('Роли')
                    ->fromModel(\Orchid\Platform\Models\Role::class, 'name')
                    ->multiple()
                    ->help('Выберите роли для пользователя'),
            ]),
        ];
    }

    public function save(Request $request, User $user): void
    {
        $data = $request->validate([
            'user.name' => 'required|string|max:255',
            'user.email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'user.password' => $user->exists ? 'nullable|string|min:8' : 'required|string|min:8',
        ]);

        $userData = $data['user'];
        
        if (empty($userData['password'])) {
            unset($userData['password']);
        }

        $user->fill($userData)->save();

        if ($request->has('user.roles')) {
            $user->syncRoles($request->input('user.roles'));
        }

        Toast::info('Пользователь успешно сохранен');
    }

    public function remove(User $user): void
    {
        $user->delete();

        Toast::info('Пользователь успешно удален');

        redirect()->route('platform.systems.users');
    }
}
