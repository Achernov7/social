<?php

namespace App\Http\Requests\Music;

use Illuminate\Foundation\Http\FormRequest;

class GetRequstRandom extends FormRequest
{

    protected function prepareForValidation(): void
    {    
        if (isset($this['search'])){
            $this['search'] = trim(html_entity_decode($this['search']), " \t\n\r\0\x0B\xC2\xA0");

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
            'search'=> 'nullable|string|max:100',
            'idsAlreadyPlayedInRandom'=> 'nullable|array',
            'idsAlreadyPlayedInRandom.*'=> 'integer'
        ];
    }

}