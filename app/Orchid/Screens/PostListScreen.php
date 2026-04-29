<?php

namespace App\Orchid\Screens;

use App\Models\Post;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PostListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'posts' => Post::with('user')
                ->filters()
                ->defaultSort('created_at', 'desc')
                ->paginate(15),
        ];
    }

    public function name(): ?string
    {
        return 'Управление публикациями';
    }

    public function description(): ?string
    {
        return 'Список всех публикаций в блоге';
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
            Link::make('Создать публикацию')
                ->icon('plus')
                ->route('platform.posts.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('posts', [
                TD::make('title', 'Заголовок')
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn (Post $post) => Link::make($post->title)
                        ->route('platform.posts.edit', $post)),

                TD::make('user.name', 'Автор')
                    ->sort()
                    ->render(fn (Post $post) => $post->user->name ?? 'Неизвестен'),

                TD::make('text', 'Текст')
                    ->render(fn (Post $post) => \Str::limit($post->text, 100)),

                TD::make('created_at', 'Дата создания')
                    ->sort()
                    ->render(fn (Post $post) => $post->created_at->format('d.m.Y H:i')),

                TD::make('updated_at', 'Обновлено')
                    ->sort()
                    ->render(fn (Post $post) => $post->updated_at->format('d.m.Y H:i')),
            ]),
        ];
    }
}
