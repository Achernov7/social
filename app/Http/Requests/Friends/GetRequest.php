<?php

namespace App\Http\Requests\Friends;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {   
        if (isset($this['additionalParams'])) {
            foreach ($this['additionalParams'] as $key=>$property) {
                if (isset($property['nameOfUserToSearch'])) {
                    if (str_contains($this['additionalParams'][$key]['nameOfUserToSearch'], '<br>')){
                        $additionalparams = $this->additionalParams;
                        $additionalparams[$key]['nameOfUserToSearch'] = str_replace('<br>', '', $this['additionalParams'][$key]['nameOfUserToSearch']);
                        $this['additionalParams'] = $additionalparams;
                    }
                }

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
            'page'=> 'required|integer',
            'limit'=> 'required|numeric|max:21',
            'alreadyTaken'=> 'array',
            'alreadyTaken.*'=> 'integer',
            'additionalParams'=> 'array',
            'additionalParams.*.nameOfUserToSearch' => 'string|max:50',
            'additionalParams.*.town' => 'string|max:60',
            'additionalParams.*.ageFrom' => 'numeric|min:14|max:80',
            'additionalParams.*.ageTo' => 'numeric|min:14|max:80',
            'additionalParams.*.gender' => 'string',
            'additionalParams.*.familystatus' => 'string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($validator->safe() as $additionalparamWithArrayWrap) {
                if (isset($validator->safe()->alreadyTaken) && $additionalparamWithArrayWrap == $validator->safe()->alreadyTaken){
                    continue;
                }
                if (is_array($additionalparamWithArrayWrap)){
                    foreach ($additionalparamWithArrayWrap as $additionalparam) {
                        if (key_exists('gender',$additionalparam)) {
                            if ($additionalparam['gender'] != 'Female' && $additionalparam['gender'] != 'Male' && $additionalparam['gender'] != 'any') {
                                $validator->errors()->add('gender', 'Gender should be male or female or any'); 
                            }
                        }
                        if (key_exists('familystatus',$additionalparam)) {
                            if ($additionalparam['familystatus'] != 'additional.Choose_status' && $additionalparam['familystatus'] != 'additional.Not_married' && $additionalparam['familystatus'] != 'additional.Married' && $additionalparam['familystatus'] != 'additional.Seeing' && $additionalparam['familystatus'] != 'additional.Engaged' && $additionalparam['familystatus'] != 'additional.In_Love' && $additionalparam['familystatus'] != 'additional.In_civil_marriage' && $additionalparam['familystatus'] != 'additional.It_s_comlicated') {
                                $validator->errors()->add('familyStatus', 'Familystatus should be additional.Choose_status, additional.Not_married, additional.Married, additional.Seeing, additional.Engaged, additional.In_Love, additional.In_civil_marriage, additional.It_s_comlicated');
                            }
                        }                    
                    }
                }
            }
        });
    }
}