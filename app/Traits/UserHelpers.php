<?php


namespace App\Traits;


use App\Enums\UserKeyTypes;
use App\Enums\UsernameTypes;
use App\Models\UserAccount;
use Illuminate\Http\Request;

trait UserHelpers
{
    private function getUsernameByField(UserAccount $user, string $usernameField): string
    {
        return $user->toArray()[$usernameField];
    }

    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }

    private function getUsernameFieldByAvailability(UserAccount $user): string
    {
        return $user->mobile_number ? UsernameTypes::MobileNumber : UsernameTypes::Email;
    }

    private function getKeyFieldFromUserKeyType(string $keyType): string
    {
        return $keyType === UserKeyTypes::pin ? 'pin_code' : 'password';
    }


}
