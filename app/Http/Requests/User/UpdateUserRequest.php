<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MobileNumber;

class UpdateUserRequest extends FormRequest
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
            'last_name' => [
                'required', 
                'max:50'
            ],
            'first_name' => [
                'required', 
                'max:50'
            ],
            'middle_name' => [
                'sometimes', 
                'max:50'
            ],
            'name_extension' => [
                'sometimes', 
                'max:50'
            ],
            'nationality_id' => [
                'required',
                'exists:nationalities,id'
            ],
            'birth_date' => [
                'required',
                'date'
            ],
            'house_no_street' => [
                'max:50'
            ],
            'provice_state' => [
                'max:50'
            ],
            'city' => [
                'max:50'
            ],
            'postal_code' => [
                'max:5'
            ],
            'place_of_birth' => [
                'max:50'
            ],
            'mother_maidenname' => [
                'max:100'
            ],
            'marital_status_id' => [
                'required',
                'exists:marital_status,id'
            ],
            'occupation' => [
                'required',
                'max:50'
            ],
            'nature_of_work_id' => [
                'required',
                'exists:natures_of_work,id'
            ],
            'source_of_fund_id' => [
                'required',
                'exists:source_of_funds,id'
            ],
            'employer' => [
                'required'
            ],
            'mobile_number' => [
                'required',
                'max:11',
                new MobileNumber()
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:50'
            ],
            'remarks' => [
                'sometimes'
            ]
        ];
    }
}
