<?php

namespace App\Http\Requests\UserProfile;

use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileBronzeRequest  extends FormRequest
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
            'birth_date'=>['required', 'before:today'],
            'country_id'=>'required',
            //'currency_id'=>'required',
            //'signup_host_id'=>'required',
            // 'verification_status'=>['required', 'max:10'],
            // 'emergency_lock_status'=>['required', 'max:10'],
            // 'report_exception_status'=>['required', 'max:10'],
            'postal_code'=>['required', 'max:5'],
            'house_no_street'=>['required', 'max:50'],
            'city'=>['required', 'max:50'],
            'province_state'=>['required', 'max:50'],
        ];
        
        $inputs = request()->input();
        
        $inputs = request()->input();
        
        if(isset($inputs['birth_date'])) {
            $birthdate = Carbon::parse($inputs['birth_date']);
            $age = $birthdate->diffInYears(Carbon::now());
            if($age < 18) {
                $required_fields_default['guardian_name'] = ['required', 'max:50'];
                $required_fields_default['guardian_mobile_number'] = ['required', 'max:11',  new MobileNumber()];
                $required_fields_default['is_accept_parental_consent'] = ['required','min:0'.'max:1', 'integer'];
            }
        }

        return $required_fields_default;
    }
}
