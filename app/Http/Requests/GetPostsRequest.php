<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPostsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'offset' => ['nullable', 'integer', 'min:0'],
            'sort_by' => ['nullable', 'string', 'in:created_at,title'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'title' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'limit.integer' => 'Limit должен быть числом',
            'limit.min' => 'Limit должен быть больше 0',
            'limit.max' => 'Limit не может быть больше 100',
            'offset.integer' => 'Offset должен быть числом',
            'offset.min' => 'Offset не может быть отрицательным',
            'sort_by.in' => 'Сортировка возможна только по created_at или title',
            'sort_order.in' => 'Порядок сортировки может быть только asc или desc',
            'date_from.date' => 'date_from должна быть корректной датой',
            'date_to.date' => 'date_to должна быть корректной датой',
            'date_to.after_or_equal' => 'date_to должна быть больше или равна date_from',
        ];
    }
}
