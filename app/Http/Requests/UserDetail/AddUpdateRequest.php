<?php

namespace App\Http\Requests\UserDetail;

use Illuminate\Foundation\Http\FormRequest;

class AddUpdateRequest extends FormRequest
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
            'entity_id'=>'required',
            'title'=>'required',
            'lastname'=>'required',
            'firstname'=>'required',
            'middlename'=>'required',
            'name_extension'=>'required',
            'birthdate'=>'required',
            'place_of_birth'=>'required',
            'maritial_status_id'=>'required',
            'nationality_id'=>'required',
            'encoded_nationality'=>'required',
            'occupation'=>'required',
            'house_no_street'=>'required',
            'city'=>'required',
            'provice_state'=>'required',
            'municipality'=>'required',
            'country_id'=>'required',
            'postal_code'=>'required',
            'nature_of_work_id'=>'required',
            'encoded_nature_of_work'=>'required',
            'source_of_fund_id'=>'required',
            'encoded_source_of_fund'=>'required',
            'mother_maidenname'=>'required',
            'currency_id'=>'required',
            'signup_host_id'=>'required',
            'verification_status'=>'required',
            'emergency_lock_status'=>'required',
            'report_exception_status'=>'required',
        ];
    }
}
