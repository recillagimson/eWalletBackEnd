<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MobileNumber implements Rule
{
    /**
     * Determines if the field is a valid philippine phone
     * number
     *
     * @var bool
     */
    private bool $validPhoneNumber = true;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->validPhoneNumber =  ((bool) preg_match('/^(09)\d{9}$/', $value));
        if(!$this->validPhoneNumber) return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        if(!$this->validPhoneNumber)
            return 'The mobile number is invalid. Use the format 09 + 9 digit mobile number.';

        return '';
    }
}
