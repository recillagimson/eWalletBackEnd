<?php

namespace App\Http\Requests\UserTransaction;

use Carbon\Carbon;
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
        $required_fields = [
            'email' => 'required|email',
            'from' => 'required|date',
            'to' => 'required|date'
        ];

        return $required_fields;
    }

    public function messages()
    {
        return [
            'to.before' => 'Date must be less than or equal to ' . Carbon::now()->format('F d, Y'),
            'from.before' => 'Date must be less than or equal to ' . Carbon::now()->format('F d, Y'),
        ];
    }
}
