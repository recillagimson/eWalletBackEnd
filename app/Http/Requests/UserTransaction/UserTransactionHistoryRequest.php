<?php

namespace App\Http\Requests\UserTransaction;

use Illuminate\Foundation\Http\FormRequest;

class UserTransactionHistoryRequest extends FormRequest
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
            'email' => 'required|email',
            'from' => 'required|date|before:today',
            'to' => 'required|date|after:today'
        ];
    }
}
