<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRequest extends FormRequest
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
            'image'=>'sometimes|file',
            'name'=>'required|string',
            'surname'=>'required|string',
            'birthdayDate'=>'required|date_format:d-m-Y',
            'town'=>'nullable|string',
            'gender'=>'required|string',
            'familyStatus'=>'nullable|string',
            'about'=>'nullable|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $birthdayDate = strtotime($validator->safe()->birthdayDate);
            $fourteenYearsOld = strtotime('-14 years',strtotime('today'));
            if ( $birthdayDate > $fourteenYearsOld ) {
                $validator->errors()->add('birthdayDate', 'You are less than 14 years old'); 
            }
        });
    }
}
