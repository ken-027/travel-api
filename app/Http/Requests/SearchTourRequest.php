<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchTourRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'date_from' => 'date',
            'date_to' => 'date',
            'price_from' => 'numeric',
            'price_to' => 'numeric',
            'sort_by' => 'in:price,name,ending_date',
            'sort_order' => 'in:desc,asc',
        ];
    }

    public function messages(): array
    {
        return [
            'sort_by' => 'The `sort_by` parameters only accepts `price`, `name` and `ending_date`',
            'sort_order' => 'The `sort_order` parameters only accepts `desc` and `asc`',
        ];
    }
}
