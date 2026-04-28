<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название публикации обязательно для заполнения',
            'title.max' => 'Название публикации не может быть длиннее 255 символов',
            'text.required' => 'Текст публикации обязателен для заполнения',
        ];
    }
}
