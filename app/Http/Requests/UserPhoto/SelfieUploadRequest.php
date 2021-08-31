<?php

namespace App\Http\Requests\UserPhoto;

use Illuminate\Foundation\Http\FormRequest;

class SelfieUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'selfie_photo' => 'required|max:5120|mimes:jpeg,png',
        ];
    }
}
