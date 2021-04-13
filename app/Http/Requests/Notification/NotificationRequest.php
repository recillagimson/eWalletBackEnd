<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
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
        'id' => 'required',
        'user_account_id' => 'required',
        'title' => 'required', 
        'status' => 'required', 
        'description' => 'required', 
        'user_created' => 'required', 
        'user_updated' => 'required', 
        ];
    }
}
