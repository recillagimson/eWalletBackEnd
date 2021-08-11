<?php

namespace App\Http\Requests\Farmer;

use App\Enums\AccountTiers;
use App\Rules\MobileNumber;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FarmerUpgradeToSilverRequest extends FormRequest
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
            'user_account_id' => ['required','exists:user_accounts,id'],
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
            'house_no_street'=>['required', 'max:100'],
            'city'=>['required', 'max:50'],
            'province_state'=>['required', 'max:50'],
            //'municipality' => 'required',
            'place_of_birth'=>['required', 'max:50'],
            'marital_status_id'=>'required',
            // 'encoded_nationality'=>'required_with:nationality_id',
            'occupation'=>['required', 'max:50'],
            'nature_of_work_id'=>'required',
            'encoded_nature_of_work'=>Rule::requiredIf($this->nature_of_work_id === '0ed96f01-9131-11eb-b44f-1c1b0d14e211'),
            'source_of_fund_id'=>'required',
            'encoded_source_of_fund'=>Rule::requiredIf($this->source_of_fund_id === '0ed801a1-9131-11eb-b44f-1c1b0d14e211'),
            'mother_maidenname'=>'required',
            'employer'=>['required', 'max:50'],
            'contact_no'=>['required', 'max:11',  new MobileNumber()],
            'rsbsa_number' => 'required'
        ];

        
        $inputs = request()->input();

        // check if first time to upgrade to silver
        if(request()->user()->tier && request()->user()->tier->id === AccountTiers::tier1) {
            $required_fields_default['id_photos_ids'] = ['required', 'array', 'min:1'];
            $required_fields_default['id_photos_ids.*'] = ['required', 'exists:user_id_photos,id'];
            $required_fields_default['id_selfie_ids'] = ['required', 'array', 'min:1'];
            $required_fields_default['id_selfie_ids.*'] = ['required', 'exists:user_selfie_photos,id'];
        }
        
        if(isset($inputs['birth_date'])) {
            $birthdate = Carbon::parse($inputs['birth_date']);
            $age = $birthdate->diffInYears(Carbon::now());
            if($age < 18) {
                $required_fields_default['guardian_name'] = ['required', 'max:50'];
                $required_fields_default['guardian_mobile_number'] = ['required', 'max:11',  new MobileNumber()];
                $required_fields_default['is_accept_parental_consent'] = ['required','min:0','max:1', 'integer'];
            }
        }

        return $required_fields_default;
    }
}
