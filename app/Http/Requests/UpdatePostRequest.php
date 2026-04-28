<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'text' => ['sometimes', 'required', 'string'],
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
