<?php

namespace App\Http\Requests\Auth;

use App\Rules\IsPasswordValid;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'mobileNumber' => 'sometimes|required|max:20|unique:useraccounts,mobileNumber',
            'email' => 'sometimes|required|max:50|email|unique:useraccounts,email',
            'password' => [
                'required',
                'min:8',
                'max:16',
                'confirmed',
                new IsPasswordValid()
            ],
            'password_confirmation' => 'required|min:8|max:16',
        ];
    }

}
