<?php

namespace App\Http\Requests\KYC;

use Illuminate\Foundation\Http\FormRequest;

class FaceMatchRequest extends FormRequest
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
            'id_photo' => 'required|max:5120|mimes:jpeg,png',
            'selfie_photo' => 'required|max:5120|mimes:jpeg,png',
            // 'user_account_id' => 'required|exists:user_accounts,id'
        ];
    }
}
