<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class GetRequestWithComments extends FormRequest
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
            'limit'=> 'required|array',
            'limit.limitPosts'=> 'required|numeric|digits_between:1,15',
            'limit.limitComments'=> 'required|numeric|digits_between:1,15',
            'limit.limitLikes'=> 'required|numeric|digits_between:1,15',
            'daysNeedToLoad' => 'required|numeric|digits_between:1,30',
            'idsOfPosts'=> 'array',
            'youSubScribeTo'=> 'nullable|array',
            'youSubScribeTo.*'=> 'integer',
            'youSubScribeToEnd'=> 'nullable|numeric|digits_between:1,30',
        ];
    }


}