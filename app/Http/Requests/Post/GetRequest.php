<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
{


    protected function prepareForValidation(): void
    {
        if (isset($this['search'])){
            if (str_contains($this['search'], '<br>')){
                $this['search'] = str_replace('<br>', '', $this['search']);
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
            'search'=> 'nullable|string|max:70',
            'page'=> 'required|integer',
            'limit'=> 'required|numeric|digits_between:1,15',
        ];
    }


}