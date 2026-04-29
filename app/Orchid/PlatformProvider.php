<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Главная')
                ->icon('bs.house')
                ->title('Навигация')
                ->route(config('platform.index')),

            Menu::make('Пользователи')
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('access admin')
                ->title('Управление'),

            Menu::make('Публикации')
                ->icon('bs.file-text')
                ->route('platform.posts')
                ->permission('access admin')
                ->divider(),

            Menu::make('Документация')
                ->title('Справка')
                ->icon('bs.box-arrow-up-right')
                ->url('https://orchid.software/en/docs')
                ->target('_blank'),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group('Система')
                ->addPermission('access admin', 'Доступ к админке'),
        ];
    }
}
