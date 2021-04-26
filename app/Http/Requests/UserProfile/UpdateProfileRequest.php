<?php

namespace App\Http\Requests\UserProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest  extends FormRequest
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
            // 'entity_id'=>'required',
            // 'title'=>['required', 'max:10'],
            'last_name'=>['required', 'max:50'],
            'first_name'=>['required', 'max:50'],
            'middle_name'=>['required', 'max:50'],
            // 'name_extension'=>['required', 'max:50'],
            'birth_date'=>'required',
            'place_of_birth'=>'required',
            'marital_status_id'=>'required',
            'nationality_id'=>'required',
            'encoded_nationality'=>'required_with:nationality_id',
            'occupation'=>'required',
            'house_no_street'=>'required',
            'city'=>'required',
            'provice_state'=>'required',
            'municipality'=>'required',
            'country_id'=>'required',
            'postal_code'=>'required',
            'nature_of_work_id'=>'required',
            'encoded_nature_of_work'=>Rule::requiredIf($this->nature_of_work_id === '0ed96f01-9131-11eb-b44f-1c1b0d14e211'),
            'source_of_fund_id'=>'required',
            'encoded_source_of_fund'=>Rule::requiredIf($this->source_of_fund_id === '0ed801a1-9131-11eb-b44f-1c1b0d14e211'),
            'mother_maidenname'=>'required',
            'currency_id'=>'required',
            'signup_host_id'=>'required',
            // 'verification_status'=>['required', 'max:10'],
            // 'emergency_lock_status'=>['required', 'max:10'],
            // 'report_exception_status'=>['required', 'max:10'],
        ];
    }
}
