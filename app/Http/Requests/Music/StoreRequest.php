<?php

namespace App\Http\Requests\Music;

use FFMpeg\FFProbe;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

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
            'music'=> 'required|mimes:mp3|max:20480',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            
            $ffprobe    = FFProbe::create();
            $duration   = $ffprobe->format($validator->safe()->music)->get('duration');
            
            if ($duration > 3600) {
                $validator->errors()->add('music', 'File must be less than 1 hour');
            }

            if (Str::length($validator->safe()->music->getClientOriginalName()) > 120) {
                $validator->errors()->add('music', 'File name must be less than 120 characters');
            }

        });
    }

}