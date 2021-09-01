<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class FarmerIdUploadRequest extends FormRequest
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
            'id_type_id' => 'required|exists:id_types,id',
            // Validate if photo in array is less that 1MB
            "id_photos"    => "required|array|min:1",
            'id_photos.*' => 'required|max:5120|mimes:jpeg,png',
            'id_number' => 'max:50',
            'user_account_id' => 'required|exists:user_accounts,id'
        ];
    }
}
