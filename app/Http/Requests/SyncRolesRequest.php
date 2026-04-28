<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => 'Список ролей обязателен для заполнения',
            'roles.array' => 'Роли должны быть переданы в виде массива',
            'roles.min' => 'Необходимо указать хотя бы одну роль',
            'roles.*.required' => 'Название роли обязательно',
            'roles.*.exists' => 'Одна или несколько указанных ролей не существуют',
        ];
    }
}
