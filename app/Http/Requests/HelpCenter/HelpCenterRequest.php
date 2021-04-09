<?php

namespace App\Http\Requests\HelpCenter;

use Illuminate\Foundation\Http\FormRequest;

class HelpCenterRequest extends FormRequest
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
            'title'=>'required',
            'description'=>'required',
            'image_location'=>'required',
            'order'=>'required',
        ];
    }
}
