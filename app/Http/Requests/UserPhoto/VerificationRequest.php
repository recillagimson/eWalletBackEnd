<?php

namespace App\Http\Requests\UserPhoto;

use Illuminate\Foundation\Http\FormRequest;

class VerificationRequest extends FormRequest
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
            'user_account_id' => 'required',
            'id_type_id' => 'required',
            'id_photo' => 'required|max:1024|mimes:jpeg,png',
            'selfie_photo' => 'required|max:1024|mimes:jpeg,png',
        ];
    }
}
