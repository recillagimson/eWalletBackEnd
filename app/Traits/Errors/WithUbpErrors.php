<?php

namespace App\Traits\Errors;

use App\Enums\ErrorCodes;

trait WithUbpErrors
{
    use WithErrors;

    public function ubpNoAccountLinked()
    {
        $this->validationErrorMessage(ErrorCodes::ubpNoAccountLinked,
            'This account is not yet linked to any UBP Bank Account');
    }

    public function ubpAccountLinkExpired()
    {
        $this->validationErrorMessage(ErrorCodes::ubpNoAccountLinked,
            'UBP Account link has expired.');
    }
}
