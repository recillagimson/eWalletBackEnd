<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MobileNumber;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Enums\AccountTiers;
use App\Services\UserAccount\IUserAccountService;

class UpdateUserRequest extends FormRequest
{
    private IUserAccountService $userAccountService;

    public function __construct(IUserAccountService $userAccountService)
    {
        $this->userAccountService = $userAccountService;
    }
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
        $user = $this->userAccountService->findById(request()->route('id'));
        
        $rules = [
            'mobile_number' => [
                Rule::requiredIf(!$this->email),
                'max:11',
                new MobileNumber(),
                'unique:user_accounts,mobile_number,' . $this->id
            ],
            'email' => [
                Rule::requiredIf(!$this->mobile_number),
                'email:rfc,dns',
                'max:50',
                'unique:user_accounts,email,' . $this->id

            ],
            'remarks' => [
                'sometimes'
            ]
        ];
        
        $bronze =  [
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
                'required',
                'max:100'
            ],
            'province_state' => [
                'required',
                'max:50'
            ],
            'city' => [
                'required',
                'max:50'
            ],
            'postal_code' => [
                'required',
                'max:5'
            ]
        ];

        $silver = [
            'place_of_birth' => [
                'required',
                'max:50'
            ],
            'mother_maidenname' => [
                'required',
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
            'encoded_nature_of_work' => [
                Rule::requiredIf($this->nature_of_work_id === '0ed96f01-9131-11eb-b44f-1c1b0d14e211')
            ],
            'source_of_fund_id' => [
                'required',
                'exists:source_of_funds,id'
            ],
            'encoded_source_of_fund' => [
                Rule::requiredIf($this->source_of_fund_id === '0ed801a1-9131-11eb-b44f-1c1b0d14e211')
            ],
            'employer' => [
                'required',
            ],
        ];

        
        $inputs = request()->input();
        
        if ($user->tier_id === AccountTiers::tier1) {
            $rules = array_merge($rules, $bronze);
        } else {
            $rules = array_merge($rules, $bronze);
            $rules = array_merge($rules, $silver);
        }
        
        if(isset($inputs['birth_date'])) {
            $birthdate = Carbon::parse($inputs['birth_date']);
            $age = $birthdate->diffInYears(Carbon::now());
            if($age < 18) {
                $rules['guardian_name'] = ['required', 'max:50'];
                $rules['guardian_mobile_number'] = ['required', 'max:11',  new MobileNumber()];
                $rules['is_accept_parental_consent'] = ['required','min:0'.'max:1', 'integer'];
            }
        }


        return $rules;
    }
}
