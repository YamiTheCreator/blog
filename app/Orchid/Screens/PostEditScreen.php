<?php

namespace App\Orchid\Screens;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PostEditScreen extends Screen
{
    public ?Post $post = null;

    public function query(Post $post): iterable
    {
        $post->load('user');

        return [
            'post' => $post,
        ];
    }

    public function name(): ?string
    {
        return $this->post->exists ? 'Редактировать публикацию' : 'Создать публикацию';
    }

    public function description(): ?string
    {
        return 'Управление публикацией в блоге';
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
                ->confirm('Вы уверены, что хотите удалить эту публикацию?')
                ->canSee($this->post->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('post.title')
                    ->title('Заголовок')
                    ->placeholder('Введите заголовок публикации')
                    ->required()
                    ->help('Название публикации'),

                TextArea::make('post.text')
                    ->title('Текст')
                    ->placeholder('Введите текст публикации')
                    ->rows(10)
                    ->required()
                    ->help('Содержание публикации'),

                Relation::make('post.user_id')
                    ->title('Автор')
                    ->fromModel(User::class, 'name')
                    ->required()
                    ->help('Выберите автора публикации'),
            ]),
        ];
    }

    public function save(Request $request, Post $post): void
    {
        $data = $request->validate([
            'post.title' => 'required|string|max:255',
            'post.text' => 'required|string',
            'post.user_id' => 'required|exists:users,id',
        ]);

        $post->fill($data['post'])->save();

        Toast::info('Публикация успешно сохранена');
    }

    public function remove(Post $post): void
    {
        $post->delete();

        Toast::info('Публикация успешно удалена');

        redirect()->route('platform.posts');
    }
}
