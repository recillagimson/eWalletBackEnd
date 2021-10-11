<?php

namespace App\Http\Requests\UserPhoto;

use Illuminate\Foundation\Http\FormRequest;

class ManualSelfieUploadRequest extends FormRequest
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
        $required_fields = [
            "tier_approval_id" => 'required|exists:tier_approvals,id',
            'selfie_photo' => 'required|max:1024|mimes:jpeg,png',
        ];

        return $required_fields;
    }
}
