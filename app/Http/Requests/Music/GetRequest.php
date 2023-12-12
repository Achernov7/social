<?php

namespace App\Http\Requests\Music;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
{

    protected function prepareForValidation(): void
    {
        if (isset($this['searchingMusic'])){
            $this['searchingMusic'] = trim(html_entity_decode($this['searchingMusic']), " \t\n\r\0\x0B\xC2\xA0");

            if (str_contains($this['searchingMusic'], '<br>')){
                $this['searchingMusic'] = str_replace('<br>', '', $this['searchingMusic']);
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
            'limit'=> 'required|numeric|max:100',
            'IdsOfAlreadyExistedSongs'=> 'nullable|array',
            'IdsOfAlreadyExistedSongs.*'=> 'required|numeric',
            'likedUsersIsOver'=> 'nullable|boolean',
            'searchingMusic'=> 'nullable|string|max:100',
        ];
    }


}