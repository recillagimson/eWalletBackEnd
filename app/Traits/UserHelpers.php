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
        $userArray = $user->toArray();
        return $userArray[$usernameField];
    }

    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }

    private function getEmailField(Request $request): ?string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : null;
    }

    private function getMobileField(Request $request): ?string
    {
        return $request->has(UsernameTypes::MobileNumber) ? UsernameTypes::MobileNumber : null;
    }

    private function getUsernameFieldByAvailability(UserAccount $user): string
    {
        return $user->mobile_number ? UsernameTypes::MobileNumber : UsernameTypes::Email;
    }

    private function getKeyFieldFromUserKeyType(string $keyType): string
    {
        return $keyType === UserKeyTypes::pin ? 'pin_code' : 'password';
    }

    private function getRecepientField(array $recipient): string
    {
        $recepientCollection = collect($recipient);
        if ($recepientCollection->has(UsernameTypes::Email))
            return UsernameTypes::Email;
        elseif ($recepientCollection->has(UsernameTypes::MobileNumber))
            return UsernameTypes::MobileNumber;
        else
            return '';
    }


}
