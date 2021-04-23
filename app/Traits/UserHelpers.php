<?php


namespace App\Traits;


use App\Enums\UserKeyTypes;
use App\Enums\UsernameTypes;
use Illuminate\Http\Request;

trait UserHelpers
{
    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }

    private function getKeyFieldFromUserKeyType(string $keyType): string
    {
        return $keyType === UserKeyTypes::pin ? 'pin_code' : 'password';
    }
}
