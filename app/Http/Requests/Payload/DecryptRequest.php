<?php

namespace App\Http\Requests\Payload;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class DecryptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return App::environment('local');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'payload' => 'required|json'
        ];
    }
}
