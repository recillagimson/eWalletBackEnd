<?php

namespace App\Http\Requests\UserProfile;

use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileSilverRequest  extends FormRequest
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
        $required_fields_default =  [
            // 'entity_id'=>'required',
            // 'title'=>['required', 'max:10'],
            'last_name'=>['required', 'max:50'],
            'first_name'=>['required', 'max:50'],
            'middle_name'=>['required', 'max:50'],
            // 'name_extension'=>['required', 'max:50'],
            'nationality_id'=>'required',
            'birth_date'=>'required',
            'country_id'=>'required',
            //'currency_id'=>'required',
            //'signup_host_id'=>'required',
            // 'verification_status'=>['required', 'max:10'],
            // 'emergency_lock_status'=>['required', 'max:10'],
            // 'report_exception_status'=>['required', 'max:10'],

            'place_of_birth'=>'required',
            'marital_status_id'=>'required',
            // 'encoded_nationality'=>'required_with:nationality_id',
            'occupation'=>'required',
            'house_no_street'=>'required',
            'city'=>'required',
            'provice_state'=>'required',
            // 'municipality'=>'required',
            'postal_code'=>'required',
            'nature_of_work_id'=>'required',
            'encoded_nature_of_work'=>Rule::requiredIf($this->nature_of_work_id === '0ed96f01-9131-11eb-b44f-1c1b0d14e211'),
            'source_of_fund_id'=>'required',
            'encoded_source_of_fund'=>Rule::requiredIf($this->source_of_fund_id === '0ed801a1-9131-11eb-b44f-1c1b0d14e211'),
            'mother_maidenname'=>'required'
        ];
        
        $inputs = request()->input();
        
        if(isset($inputs['birth_date'])) {
            $birthdate = Carbon::parse($inputs['birth_date']);
            $age = $birthdate->diffInYears(Carbon::now());
            if($age < 18) {
                $required_fields_default['guardian_name'] = 'required';
                $required_fields_default['guardian_mobile_number'] = 'required';
                $required_fields_default['is_accept_parental_consent'] = 'required';
            }
        }

        return $required_fields_default;
    }
}
