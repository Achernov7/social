<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class GetRequestForSubscribersOrBanUsers extends FormRequest
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
            'searchingUser'=> 'nullable|string|max:70',
            'page'=> 'required|integer',
            'limit'=> 'required|numeric|digits_between:1,15',
            'IdsOfAlreadyExistedUsers'=> 'nullable|array',
        ];
    }

}