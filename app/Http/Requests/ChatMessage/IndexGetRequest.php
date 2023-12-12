<?php

namespace App\Http\Requests\ChatMessage;

use Illuminate\Foundation\Http\FormRequest;

class IndexGetRequest extends FormRequest
{

    protected function prepareForValidation(): void
    {
        if (isset($this['searchingUser'])){
            if (str_contains($this['searchingUser'], '<br>')){
                $this['searchingUser'] = str_replace('<br>', '', $this['searchingUser']);
            }
        }
    }

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
            'limit'=> 'required|numeric|max:10',
            'DialogIds'=> 'array',
            'DialogIds.*'=> 'integer',
            'latestTimestamp'=> 'integer',
            'searchingUser'=> 'nullable|string|max:75'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (str_contains($validator->safe()->searchingUser, 'select')){
                $validator->errors()->add('searchingUser', 'searchingUser should not contain select'); 
            }
        });
    }
}