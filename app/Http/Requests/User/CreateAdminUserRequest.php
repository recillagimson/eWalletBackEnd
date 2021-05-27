<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'last_name' => 'required|max:50',
            'first_name' => 'required|max:50',
            'middle_name' => 'max:50',
            'birth_date' => 'required|date',
            'marital_status_id' => 'required',
            'house_no_street' => 'required',
            'city' => 'required',
            'province_state' => 'required',
            'municipality' => 'required',
            'country_id' => 'required',
        ];
    }
}
