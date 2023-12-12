<?php

namespace App\Http\Requests\ChatMessage;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'page'=> 'required|integer',
            'limit'=> 'required|numeric|max:10',
            'IdsOfMessages'=> 'array',
            'IdsOfMessages.*'=> 'integer',
            'date'=> 'date',
        ];
    }
}