<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class DAPersonelRequest extends FormRequest
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
            'name_of_da_personel' => 'required',
            'da_remarks' => 'required',
            "is_da_update" => 'nullable',
            "user_account_id" => 'required'
        ];
    }
}
